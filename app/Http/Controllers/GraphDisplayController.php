<?php

namespace App\Http\Controllers;

use App\Models\FlowReading;
use App\Models\FloodReading;
use App\Models\RainReading;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GraphDisplayController extends Controller
{
    public function index(): View
    {
        return view('contents.graph-display', [
            'initialPayload' => $this->buildPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->buildPayload());
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $flowLatest = FlowReading::query()
            ->orderByDesc('created_at')
            ->first();

        $flowLastHour = FlowReading::query()
            ->where('created_at', '>=', now()->subHour())
            ->orderBy('created_at')
            ->get(['flow_lpm', 'total_ml', 'created_at']);

        $flowLast24h = FlowReading::query()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at')
            ->get(['flow_lpm', 'total_ml', 'created_at']);

        $rainLatest = RainReading::query()
            ->orderByDesc('created_at')
            ->first();

        $rainLastHour = RainReading::query()
            ->where('created_at', '>=', now()->subHour())
            ->orderBy('created_at')
            ->get(['analog_value', 'intensity_level', 'created_at']);

        $rainLast24h = RainReading::query()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at')
            ->get(['analog_value', 'intensity_level', 'created_at']);

        $floodLatest = FloodReading::query()
            ->orderByDesc('created_at')
            ->first();

        $floodLastHour = FloodReading::query()
            ->where('created_at', '>=', now()->subHour())
            ->orderBy('created_at')
            ->get(['status', 'rise_time_sec', 'created_at']);

        $floodLast24h = FloodReading::query()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at')
            ->get(['status', 'rise_time_sec', 'created_at']);

        return [
            'flow' => [
                'latest' => $flowLatest ? [
                    'flow_lpm' => (float) $flowLatest->flow_lpm,
                    'total_ml' => (int) $flowLatest->total_ml,
                    'measured_at' => optional($flowLatest->created_at)->toIso8601String(),
                ] : null,
                'series' => [
                    'hour' => $flowLastHour->map(fn (FlowReading $reading): array => [
                        'flow_lpm' => (float) $reading->flow_lpm,
                        'total_ml' => (int) $reading->total_ml,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                    'day' => $flowLast24h->map(fn (FlowReading $reading): array => [
                        'flow_lpm' => (float) $reading->flow_lpm,
                        'total_ml' => (int) $reading->total_ml,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                ],
                'stats' => [
                    'hour_avg_flow' => round((float) ($flowLastHour->avg('flow_lpm') ?? 0), 3),
                    'day_peak_flow' => round((float) ($flowLast24h->max('flow_lpm') ?? 0), 3),
                ],
            ],
            'rain' => [
                'latest' => $rainLatest ? [
                    'analog_value' => (int) $rainLatest->analog_value,
                    'intensity_level' => $rainLatest->intensity_level,
                    'measured_at' => optional($rainLatest->created_at)->toIso8601String(),
                ] : null,
                'series' => [
                    'hour' => $rainLastHour->map(fn (RainReading $reading): array => [
                        'analog_value' => (int) $reading->analog_value,
                        'intensity_level' => $reading->intensity_level,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                    'day' => $rainLast24h->map(fn (RainReading $reading): array => [
                        'analog_value' => (int) $reading->analog_value,
                        'intensity_level' => $reading->intensity_level,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                ],
                'stats' => [
                    'hour_avg_analog' => round((float) ($rainLastHour->avg('analog_value') ?? 0), 1),
                    'day_peak_analog' => (int) ($rainLast24h->max('analog_value') ?? 0),
                    'hour_rain_count' => (int) $rainLastHour->whereIn('intensity_level', ['rain', 'heavy_rain'])->count(),
                    'hour_heavy_count' => (int) $rainLastHour->where('intensity_level', 'heavy_rain')->count(),
                ],
            ],
            'flood' => [
                'latest' => $floodLatest ? [
                    'status' => (string) $floodLatest->status,
                    'rise_time_sec' => (int) $floodLatest->rise_time_sec,
                    'measured_at' => optional($floodLatest->created_at)->toIso8601String(),
                ] : null,
                'series' => [
                    'hour' => $floodLastHour->map(fn (FloodReading $reading): array => [
                        'status' => (string) $reading->status,
                        'rise_time_sec' => (int) $reading->rise_time_sec,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                    'day' => $floodLast24h->map(fn (FloodReading $reading): array => [
                        'status' => (string) $reading->status,
                        'rise_time_sec' => (int) $reading->rise_time_sec,
                        'measured_at' => optional($reading->created_at)->toIso8601String(),
                    ])->all(),
                ],
                'stats' => [
                    'hour_warning_count' => (int) $floodLastHour
                        ->whereIn('status', ['LEVEL 1 DETECTED', 'NORMAL RISE', 'FLASH FLOOD WARNING', 'CRITICAL'])
                        ->count(),
                    'hour_critical_count' => (int) $floodLastHour->where('status', 'CRITICAL')->count(),
                ],
            ],
        ];
    }
}
