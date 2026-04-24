<?php

namespace App\Http\Controllers;

use App\Models\FloodReading;
use App\Models\FlowReading;
use App\Models\RainReading;
use App\Models\Sensor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiInsightController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->isPro()) {
            return redirect()->route('plans')
                ->with('error', 'AI insights are available on the Pro plan.');
        }

        $now = now();
        $lastHour = $now->copy()->subHour();

        $latestFlood = FloodReading::query()->latest('created_at')->first();
        $latestRain = RainReading::query()->latest('created_at')->first();
        $recentFlow = FlowReading::query()->where('created_at', '>=', $now->copy()->subMinutes(20))->latest('created_at')->limit(30)->get();

        $flowAvg = round((float) ($recentFlow->avg('flow_lpm') ?? 0), 3);
        $flowMax = round((float) ($recentFlow->max('flow_lpm') ?? 0), 3);
        $flowMin = round((float) ($recentFlow->min('flow_lpm') ?? 0), 3);

        $heavyRainCount = RainReading::query()
            ->where('created_at', '>=', $lastHour)
            ->where('intensity_level', 'heavy_rain')
            ->count();

        $floodWarningCount = FloodReading::query()
            ->where('created_at', '>=', $lastHour)
            ->whereIn('status', ['NORMAL RISE', 'FLASH FLOOD WARNING', 'CRITICAL'])
            ->count();

        $onlineSensors = Sensor::query()
            ->where('is_active', true)
            ->where('last_seen_at', '>=', $now->copy()->subMinutes(2))
            ->count();

        $floodStatus = (string) ($latestFlood?->status ?? 'SAFE / DRY');
        $rainLevel = (string) ($latestRain?->intensity_level ?? 'no_rain');

        $riskScore = 8;

        if (in_array($floodStatus, ['LEVEL 1 DETECTED', 'NORMAL RISE'], true)) {
            $riskScore += 24;
        }

        if (in_array($floodStatus, ['FLASH FLOOD WARNING', 'CRITICAL'], true)) {
            $riskScore += 42;
        }

        if ($rainLevel === 'rain') {
            $riskScore += 15;
        }

        if ($rainLevel === 'heavy_rain') {
            $riskScore += 28;
        }

        $riskScore += min(18, $floodWarningCount * 2);
        $riskScore += min(14, $heavyRainCount * 3);

        if ($onlineSensors < 3) {
            $riskScore += 8;
        }

        $riskScore = max(0, min(100, $riskScore));

        $riskLabel = 'Low';
        if ($riskScore >= 40) {
            $riskLabel = 'Moderate';
        }
        if ($riskScore >= 70) {
            $riskLabel = 'High';
        }

        $recommendations = collect();

        if (in_array($floodStatus, ['FLASH FLOOD WARNING', 'CRITICAL'], true)) {
            $recommendations->push('Trigger immediate warning broadcast and verify evacuation channels.');
        }

        if ($rainLevel === 'heavy_rain' || $heavyRainCount >= 4) {
            $recommendations->push('Increase rainfall sampling attention for the next 60 minutes.');
        }

        if ($flowMax > 0 && ($flowMax - $flowMin) > max(1, $flowAvg * 1.2)) {
            $recommendations->push('Flow volatility is elevated; inspect intake/outflow path for sudden blockage changes.');
        }

        if ($onlineSensors < 3) {
            $recommendations->push('One or more sensors appear stale; check device power and connectivity.');
        }

        if ($recommendations->isEmpty()) {
            $recommendations->push('Conditions are stable. Keep regular monitoring cadence and verify alerts every 15 minutes.');
        }

        return view('contents.ai-insights', [
            'insights' => [
                'risk_score' => $riskScore,
                'risk_label' => $riskLabel,
                'flood_status' => $floodStatus,
                'rain_level' => str_replace('_', ' ', $rainLevel),
                'flow_avg' => $flowAvg,
                'flow_min' => $flowMin,
                'flow_max' => $flowMax,
                'flood_warning_count' => $floodWarningCount,
                'heavy_rain_count' => $heavyRainCount,
                'online_sensors' => $onlineSensors,
                'generated_at' => now(),
            ],
            'recommendations' => $recommendations,
        ]);
    }
}
