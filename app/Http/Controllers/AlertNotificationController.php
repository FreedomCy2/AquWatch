<?php

namespace App\Http\Controllers;

use App\Models\FlowReading;
use App\Models\FloodReading;
use App\Models\RainReading;
use App\Models\Sensor;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertNotificationController extends Controller
{
    public function index(): View
    {
        $preferences = $this->resolveAlertPreferences();
        $alerts = $this->buildAlerts(12, $preferences);

        // Opening notifications marks currently visible alerts as seen.
        session(['alerts_seen_at' => now()->toIso8601String()]);

        return view('contents.notifications', [
            'alerts' => $alerts,
            'isHistory' => false,
            'summary' => [
                'critical' => $alerts->where('severity', 'critical')->count(),
                'warning' => $alerts->where('severity', 'warning')->count(),
                'info' => $alerts->where('severity', 'info')->count(),
            ],
        ]);
    }

    public function history(): View
    {
        $preferences = $this->resolveAlertPreferences();
        $alerts = $this->buildAlerts(120, $preferences);

        session(['alerts_seen_at' => now()->toIso8601String()]);

        return view('contents.notifications', [
            'alerts' => $alerts,
            'isHistory' => true,
            'summary' => [
                'critical' => $alerts->where('severity', 'critical')->count(),
                'warning' => $alerts->where('severity', 'warning')->count(),
                'info' => $alerts->where('severity', 'info')->count(),
            ],
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildAlerts(int $limit = 80, array $preferences = []): Collection
    {
        $alerts = collect();
        $now = CarbonImmutable::now();
        $retentionDays = max(1, (int) ($preferences['retention_days'] ?? 90));
        $retentionCutoff = $now->subDays($retentionDays);
        $floodTrigger = (string) ($preferences['flood_trigger'] ?? 'LEVEL 1 DETECTED');
        $rainTrigger = (string) ($preferences['rain_trigger'] ?? 'heavy_rain');
        $flowAnomalyPercent = max(5, (int) ($preferences['flow_anomaly_percent'] ?? 30));
        $escalationContact = trim((string) ($preferences['escalation_contact'] ?? ''));
        $quietHoursActive = $this->isWithinQuietHours(
            (string) ($preferences['quiet_hours_start'] ?? ''),
            (string) ($preferences['quiet_hours_end'] ?? ''),
            (string) (optional(Auth::user())->timezone ?: config('app.timezone'))
        );

        $floodSeverity = [
            'CRITICAL' => 'critical',
            'FLASH FLOOD WARNING' => 'critical',
            'NORMAL RISE' => 'warning',
            'LEVEL 1 DETECTED' => 'warning',
        ];

        $floodReadings = FloodReading::query()
            ->select(['sensor_id', 'status', 'rise_time_sec', 'created_at'])
            ->where('created_at', '>=', $retentionCutoff)
            ->latest('created_at')
            ->limit(200)
            ->get()
            ->groupBy('sensor_id');

        foreach ($floodReadings as $sensorReadings) {
            $ordered = $sensorReadings->sortBy('created_at')->values();
            $previousStatus = 'SAFE / DRY';

            foreach ($ordered as $reading) {
                $status = (string) $reading->status;

                if (! array_key_exists($status, $floodSeverity)) {
                    $previousStatus = $status;
                    continue;
                }

                if ($status !== $previousStatus && $this->shouldTriggerFloodAlert($status, $floodTrigger)) {
                    $alerts->push([
                        'source' => 'flood',
                        'severity' => $floodSeverity[$status] ?? 'warning',
                        'title' => 'Flood sensor alert',
                        'message' => sprintf(
                            '%s reported %s (rise: %ss).',
                            (string) $reading->sensor_id,
                            $status,
                            (int) $reading->rise_time_sec
                        ),
                        'occurred_at' => $reading->created_at,
                    ]);
                }

                $previousStatus = $status;
            }
        }

        $rainReadings = RainReading::query()
            ->select(['sensor_id', 'intensity_level', 'analog_value', 'created_at'])
            ->where('created_at', '>=', $retentionCutoff)
            ->latest('created_at')
            ->limit(200)
            ->get()
            ->groupBy('sensor_id');

        foreach ($rainReadings as $sensorReadings) {
            $ordered = $sensorReadings->sortBy('created_at')->values();
            $previousLevel = 'no_rain';

            foreach ($ordered as $reading) {
                $level = (string) $reading->intensity_level;

                if (! in_array($level, ['rain', 'heavy_rain'], true)) {
                    $previousLevel = $level;
                    continue;
                }

                if ($level !== $previousLevel && $this->shouldTriggerRainAlert($level, $rainTrigger)) {
                    $isHeavy = $level === 'heavy_rain';

                    $alerts->push([
                        'source' => 'rain',
                        'severity' => $isHeavy ? 'critical' : 'warning',
                        'title' => $isHeavy ? 'Heavy rain detected' : 'Rain detected',
                        'message' => sprintf(
                            '%s reported %s (value: %s).',
                            (string) $reading->sensor_id,
                            str_replace('_', ' ', $level),
                            number_format((int) $reading->analog_value)
                        ),
                        'occurred_at' => $reading->created_at,
                    ]);
                }

                $previousLevel = $level;
            }
        }

        $flowReadings = FlowReading::query()
            ->select(['sensor_id', 'flow_lpm', 'measured_at'])
            ->where('measured_at', '>=', $retentionCutoff)
            ->latest('measured_at')
            ->limit(300)
            ->get()
            ->groupBy('sensor_id');

        foreach ($flowReadings as $sensorReadings) {
            $ordered = $sensorReadings->sortBy('measured_at')->values();
            $previousFlow = null;

            foreach ($ordered as $reading) {
                $currentFlow = (float) $reading->flow_lpm;

                if ($previousFlow === null) {
                    $previousFlow = $currentFlow;
                    continue;
                }

                $denominator = max(abs($previousFlow), 0.001);
                $changePercent = abs(($currentFlow - $previousFlow) / $denominator) * 100;

                if ($changePercent >= $flowAnomalyPercent) {
                    $alerts->push([
                        'source' => 'flow',
                        'severity' => $changePercent >= ($flowAnomalyPercent * 2) ? 'critical' : 'warning',
                        'title' => 'Flow anomaly detected',
                        'message' => sprintf(
                            '%s changed by %.1f%% (from %.2f to %.2f L/min, threshold: %d%%).',
                            (string) $reading->sensor_id,
                            $changePercent,
                            $previousFlow,
                            $currentFlow,
                            $flowAnomalyPercent
                        ),
                        'occurred_at' => $reading->measured_at,
                    ]);
                }

                $previousFlow = $currentFlow;
            }
        }

        $offlineCutoff = now()->subMinutes(2);

        $offlineSensors = Sensor::query()
            ->select(['sensor_id', 'sensor_type', 'last_seen_at'])
            ->where('is_active', true)
            ->where(function ($query) use ($offlineCutoff): void {
                $query->whereNull('last_seen_at')
                    ->orWhere('last_seen_at', '<', $offlineCutoff);
            })
            ->limit(10)
            ->get();

        foreach ($offlineSensors as $sensor) {
            $lastSeenText = $sensor->last_seen_at ? $sensor->last_seen_at->diffForHumans() : 'never';

            $alerts->push([
                'source' => (string) ($sensor->sensor_type ?: 'sensor'),
                'severity' => 'info',
                'title' => 'Sensor connection stale',
                'message' => sprintf(
                    '%s has no recent heartbeat (last seen: %s).',
                    (string) $sensor->sensor_id,
                    $lastSeenText
                ),
                'occurred_at' => $sensor->last_seen_at,
            ]);
        }

        if ($quietHoursActive) {
            $beforeCount = $alerts->count();

            $alerts = $alerts
                ->filter(fn (array $alert): bool => ($alert['severity'] ?? 'info') === 'critical')
                ->values();

            $suppressedCount = $beforeCount - $alerts->count();

            if ($suppressedCount > 0) {
                $alerts->push([
                    'source' => 'system',
                    'severity' => 'info',
                    'title' => 'Quiet hours active',
                    'message' => sprintf('%d non-critical alerts were suppressed during quiet hours.', $suppressedCount),
                    'occurred_at' => $now,
                ]);
            }
        }

        if ($escalationContact !== '') {
            $alerts = $alerts->map(function (array $alert) use ($escalationContact): array {
                if (($alert['severity'] ?? null) !== 'critical') {
                    return $alert;
                }

                $alert['message'] = rtrim((string) ($alert['message'] ?? ''))
                    .' Escalation contact: '.$escalationContact.'.';

                return $alert;
            });
        }

        return $alerts
            ->sortByDesc(function (array $alert): int {
                $time = $alert['occurred_at'];

                return $time ? $time->getTimestamp() : 0;
            })
            ->take($limit)
            ->values();
    }

    private function resolveAlertPreferences(): array
    {
        $preferences = (array) (optional(optional(Auth::user())->profile)->preferences ?? []);

        return [
            'flood_trigger' => (string) ($preferences['flood_trigger'] ?? 'LEVEL 1 DETECTED'),
            'rain_trigger' => (string) ($preferences['rain_trigger'] ?? 'heavy_rain'),
            'flow_anomaly_percent' => (int) ($preferences['flow_anomaly_percent'] ?? 30),
            'quiet_hours_start' => $preferences['quiet_hours_start'] ?? null,
            'quiet_hours_end' => $preferences['quiet_hours_end'] ?? null,
            'escalation_contact' => $preferences['escalation_contact'] ?? null,
            'retention_days' => (int) ($preferences['retention_days'] ?? 90),
        ];
    }

    private function shouldTriggerFloodAlert(string $status, string $trigger): bool
    {
        $levels = [
            'LEVEL 1 DETECTED' => 1,
            'NORMAL RISE' => 2,
            'FLASH FLOOD WARNING' => 3,
            'CRITICAL' => 4,
        ];

        if (! isset($levels[$status], $levels[$trigger])) {
            return false;
        }

        return $levels[$status] >= $levels[$trigger];
    }

    private function shouldTriggerRainAlert(string $level, string $trigger): bool
    {
        $levels = [
            'rain' => 1,
            'heavy_rain' => 2,
        ];

        if (! isset($levels[$level], $levels[$trigger])) {
            return false;
        }

        return $levels[$level] >= $levels[$trigger];
    }

    private function isWithinQuietHours(string $start, string $end, string $timezone): bool
    {
        if ($start === '' || $end === '') {
            return false;
        }

        try {
            $now = CarbonImmutable::now($timezone);

            [$startHour, $startMinute] = array_map('intval', explode(':', $start));
            [$endHour, $endMinute] = array_map('intval', explode(':', $end));

            $startAt = $now->setTime($startHour, $startMinute);
            $endAt = $now->setTime($endHour, $endMinute);
        } catch (\Throwable) {
            return false;
        }

        if ($startAt->equalTo($endAt)) {
            return false;
        }

        if ($endAt->greaterThan($startAt)) {
            return $now->greaterThanOrEqualTo($startAt) && $now->lessThan($endAt);
        }

        return $now->greaterThanOrEqualTo($startAt) || $now->lessThan($endAt);
    }
}
