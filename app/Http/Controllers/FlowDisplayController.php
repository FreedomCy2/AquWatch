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
        $liveCutoff = $now->copy()->subSeconds(15);

        $latest = FlowReading::query()
            ->orderByDesc('created_at')
            ->first();

        return [
            'latest' => $latest ? [
                'sensor_id' => $latest->sensor_id,
                'flow_lpm' => (float) $latest->flow_lpm,
                'total_ml' => (int) $latest->total_ml,
                'measured_at' => optional($latest->created_at)->toIso8601String(),
                'is_recent' => optional($latest->created_at)->greaterThanOrEqualTo($liveCutoff) ?? false,
            ] : null,
            'stats' => [
                'reading_count' => FlowReading::count(),
                'avg_flow_lpm' => round((float) (FlowReading::query()->avg('flow_lpm') ?? 0), 3),
                'max_flow_lpm' => round((float) (FlowReading::query()->max('flow_lpm') ?? 0), 3),
            ],
        ];
    }
}
