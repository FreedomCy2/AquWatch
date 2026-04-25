<?php

namespace App\Http\Controllers;

use App\Models\FlowReading;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FlowDisplayController extends Controller
{
    public function index(): View
    {
        return view('contents.flow-display', [
            'initialPayload' => $this->buildPayload(),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json($this->buildPayload());
    }

    public function readings(): View
    {
        return view('contents.flow-readings', [
            'readings' => FlowReading::query()
                ->select(['id', 'sensor_id', 'flow_lpm', 'total_ml', 'created_at'])
                ->orderByDesc('created_at')
                ->simplePaginate(25),
        ]);
    }

    /**
    * Build a compact payload for summary KPI cards.
     *
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $now = now();
        $liveCutoff = $now->copy()->subMinutes(2);

        $latestPerSensor = FlowReading::query()
            ->select(['sensor_id', 'flow_lpm', 'total_ml', 'created_at'])
            ->orderByDesc('created_at')
            ->get()
            ->unique('sensor_id')
            ->take(2)
            ->values()
            ->map(function (FlowReading $reading) use ($liveCutoff): array {
                $isRecent = optional($reading->created_at)->greaterThanOrEqualTo($liveCutoff) ?? false;

                return [
                    'sensor_id' => (string) $reading->sensor_id,
                    'flow_lpm' => $isRecent ? (float) $reading->flow_lpm : 0.0,
                    'total_ml' => (int) $reading->total_ml,
                    'measured_at' => optional($reading->created_at)->toIso8601String(),
                    'is_recent' => $isRecent,
                ];
            })
            ->all();

        $recentSensors = collect($latestPerSensor)
            ->filter(fn (array $sensor): bool => (bool) ($sensor['is_recent'] ?? false))
            ->values();

        $combinedFlow = $recentSensors
            ->sum(fn (array $sensor): float => (float) ($sensor['flow_lpm'] ?? 0));

        $combinedTotal = collect($latestPerSensor)
            ->sum(fn (array $sensor): int => (int) ($sensor['total_ml'] ?? 0));

        $lastUpdated = $recentSensors
            ->pluck('measured_at')
            ->filter()
            ->first();

        return [
            'sensors' => $latestPerSensor,
            'combined' => [
                'flow_lpm' => round((float) $combinedFlow, 3),
                'total_ml' => (int) $combinedTotal,
                'measured_at' => $lastUpdated,
                'sensor_count' => $recentSensors->count(),
                'is_recent' => $recentSensors->isNotEmpty(),
            ],
            'stats' => [
                'reading_count' => FlowReading::count(),
                'avg_flow_lpm' => round((float) (FlowReading::query()->avg('flow_lpm') ?? 0), 3),
                'max_flow_lpm' => round((float) (FlowReading::query()->max('flow_lpm') ?? 0), 3),
            ],
        ];
    }
}
