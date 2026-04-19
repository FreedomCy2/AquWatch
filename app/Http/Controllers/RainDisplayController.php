<?php

namespace App\Http\Controllers;

use App\Models\RainReading;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RainDisplayController extends Controller
{
    public function index(): View
    {
        return view('contents.rain-display', [
            'initialPayload' => $this->buildPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->buildPayload());
    }

    public function readings(): View
    {
        return view('contents.rain-readings', [
            'readings' => RainReading::query()
                ->select(['id', 'sensor_id', 'analog_value', 'intensity_level', 'created_at'])
                ->orderByDesc('created_at')
                ->simplePaginate(25),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $latest = RainReading::query()
            ->orderByDesc('created_at')
            ->first();

        return [
            'latest' => $latest ? [
                'sensor_id' => $latest->sensor_id,
                'analog_value' => (int) $latest->analog_value,
                'intensity_level' => $latest->intensity_level,
                'measured_at' => optional($latest->created_at)->toIso8601String(),
                'is_recent' => optional($latest->created_at)->greaterThanOrEqualTo(now()->subSeconds(20)) ?? false,
            ] : null,
            'stats' => [
                'reading_count' => RainReading::count(),
                'last_hour_rain_count' => RainReading::query()->where('created_at', '>=', now()->subHour())->whereIn('intensity_level', ['rain', 'heavy_rain'])->count(),
                'last_hour_heavy_count' => RainReading::query()->where('created_at', '>=', now()->subHour())->where('intensity_level', 'heavy_rain')->count(),
                'avg_analog_value' => round((float) (RainReading::query()->avg('analog_value') ?? 0), 1),
            ],
        ];
    }
}
