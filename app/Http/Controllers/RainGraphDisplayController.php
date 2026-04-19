<?php

namespace App\Http\Controllers;

use App\Models\RainReading;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RainGraphDisplayController extends Controller
{
    public function index(): View
    {
        return view('contents.rain-graph-display', [
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
        $latest = RainReading::query()
            ->orderByDesc('created_at')
            ->first();

        $lastHour = RainReading::query()
            ->where('created_at', '>=', now()->subHour())
            ->orderBy('created_at')
            ->get(['analog_value', 'intensity_level', 'created_at']);

        $last24h = RainReading::query()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at')
            ->get(['analog_value', 'intensity_level', 'created_at']);

        return [
            'latest' => $latest ? [
                'analog_value' => (int) $latest->analog_value,
                'intensity_level' => $latest->intensity_level,
                'measured_at' => optional($latest->created_at)->toIso8601String(),
            ] : null,
            'series' => [
                'hour' => $lastHour->map(fn (RainReading $reading): array => [
                    'analog_value' => (int) $reading->analog_value,
                    'intensity_level' => $reading->intensity_level,
                    'measured_at' => optional($reading->created_at)->toIso8601String(),
                ])->all(),
                'day' => $last24h->map(fn (RainReading $reading): array => [
                    'analog_value' => (int) $reading->analog_value,
                    'intensity_level' => $reading->intensity_level,
                    'measured_at' => optional($reading->created_at)->toIso8601String(),
                ])->all(),
            ],
            'stats' => [
                'hour_avg_analog' => round((float) ($lastHour->avg('analog_value') ?? 0), 1),
                'day_peak_analog' => (int) ($last24h->max('analog_value') ?? 0),
                'hour_heavy_count' => (int) $lastHour->where('intensity_level', 'heavy_rain')->count(),
            ],
        ];
    }
}
