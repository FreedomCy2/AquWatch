<?php

namespace App\Http\Controllers;

use App\Models\FloodReading;
use App\Models\RainReading;
use App\Models\Sensor;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AlertNotificationController extends Controller
{
    public function index(): View
    {
        $alerts = $this->buildAlerts(12);

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
        $alerts = $this->buildAlerts(120);

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
    private function buildAlerts(int $limit = 80): Collection
    {
        $alerts = collect();

        $floodSeverity = [
            'CRITICAL' => 'critical',
            'FLASH FLOOD WARNING' => 'critical',
            'NORMAL RISE' => 'warning',
            'LEVEL 1 DETECTED' => 'warning',
        ];

        $floodReadings = FloodReading::query()
            ->select(['sensor_id', 'status', 'rise_time_sec', 'created_at'])
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

                if ($status !== $previousStatus) {
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

                if ($level !== $previousLevel) {
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

        return $alerts
            ->sortByDesc(function (array $alert): int {
                $time = $alert['occurred_at'];

                return $time ? $time->getTimestamp() : 0;
            })
            ->take($limit)
            ->values();
    }
}
