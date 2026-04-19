<?php

namespace App\Http\Controllers;

use App\Models\FloodReading;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FloodDisplayController extends Controller
{
    public function index(): View
    {
        return view('contents.flood-display', [
            'initialPayload' => $this->buildPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->buildPayload());
    }

    public function readings(): View
    {
        return view('contents.flood-readings', [
            'readings' => FloodReading::query()
                ->select(['id', 'sensor_id', 'status', 's1_wet', 's2_wet', 's3_wet', 'rise_time_sec', 'created_at'])
                ->orderByDesc('created_at')
                ->simplePaginate(25),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $latest = FloodReading::query()
            ->orderByDesc('created_at')
            ->first();

        return [
            'latest' => $latest ? [
                'sensor_id' => $latest->sensor_id,
                'status' => $latest->status,
                's1_wet' => (bool) $latest->s1_wet,
                's2_wet' => (bool) $latest->s2_wet,
                's3_wet' => (bool) $latest->s3_wet,
                'rise_time_sec' => (int) $latest->rise_time_sec,
                'measured_at' => optional($latest->created_at)->toIso8601String(),
                'is_recent' => optional($latest->created_at)->greaterThanOrEqualTo(now()->subSeconds(20)) ?? false,
            ] : null,
            'stats' => [
                'reading_count' => FloodReading::count(),
                'last_hour_warning_count' => FloodReading::query()
                    ->where('created_at', '>=', now()->subHour())
                    ->whereIn('status', ['NORMAL RISE', 'FLASH FLOOD WARNING', 'CRITICAL'])
                    ->count(),
                'last_hour_critical_count' => FloodReading::query()
                    ->where('created_at', '>=', now()->subHour())
                    ->where('status', 'CRITICAL')
                    ->count(),
            ],
        ];
    }
}
