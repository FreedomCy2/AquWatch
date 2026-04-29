<?php

namespace App\Http\Controllers;

use App\Models\FloodReading;
use App\Models\FlowReading;
use App\Models\RainReading;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class AiChatController extends Controller
{
    private const HISTORY_SESSION_KEY = 'ai_chat.history';

    private const MAX_HISTORY_MESSAGES = 12;

    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->isPro()) {
            return redirect()->route('plans')
                ->with('error', 'AI chat is available on the Pro plan.');
        }

        return view('contents.ai-chat', [
            'chatHistory' => $this->getChatHistory(),
        ]);
    }

    public function ask(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->isPro()) {
            return response()->json([
                'ok' => false,
                'message' => 'AI chat is available on the Pro plan only.',
            ], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $question = trim((string) $validated['message']);
        $lowerQuestion = strtolower($question);

        $latestFlood = FloodReading::query()->latest('created_at')->first();
        $latestRain = RainReading::query()->latest('created_at')->first();
        $latestFlow = FlowReading::query()->latest('created_at')->first();

        $activeSensors = Sensor::query()
            ->where('is_active', true)
            ->where('last_seen_at', '>=', now()->subSeconds(30))
            ->count();

        $floodStatus = (string) ($latestFlood?->status ?? 'SAFE / DRY');
        $rainStatus = (string) ($latestRain?->intensity_level ?? 'no_rain');
        $flowNow = (float) ($latestFlow?->flow_lpm ?? 0);

        $context = $this->buildContext(
            $floodStatus,
            $rainStatus,
            $flowNow,
            $activeSensors,
            $latestFlood?->created_at?->diffForHumans(),
            $latestRain?->created_at?->diffForHumans(),
            $latestFlow?->created_at?->diffForHumans()
        );

        $historyMessages = $this->historyForLlm();

        $answer = $this->askLlm($question, $context, $historyMessages)
            ?? $this->buildAnswer(
                $lowerQuestion,
                $floodStatus,
                $rainStatus,
                $flowNow,
                $activeSensors,
                $latestFlood?->created_at?->diffForHumans(),
                $latestRain?->created_at?->diffForHumans(),
                $latestFlow?->created_at?->diffForHumans()
            );

        $this->storeChatTurn($question, $answer);

        return response()->json([
            'ok' => true,
            'answer' => $answer,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    public function clear(): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->isPro()) {
            return response()->json([
                'ok' => false,
                'message' => 'AI chat is available on the Pro plan only.',
            ], 403);
        }

        session()->forget(self::HISTORY_SESSION_KEY);

        return response()->json([
            'ok' => true,
        ]);
    }

    /**
     * @param  array<string, string|int|float>  $context
     */
    private function askLlm(string $question, array $context, array $historyMessages): ?string
    {
        $provider = (string) config('services.ai.provider', 'openai');
        $apiKey = (string) config('services.ai.api_key', '');

        if ($provider !== 'openai' || $apiKey === '') {
            return null;
        }

        $model = (string) config('services.ai.model', 'gpt-4o-mini');
        $baseUrl = rtrim((string) config('services.ai.base_url', 'https://api.openai.com/v1'), '/');

        $systemPrompt = 'You are AquWatch AI assistant. Be conversational and friendly like a helpful chat assistant. '
            .'Prioritize AquWatch operations: flood/rain/flow/sensors/risk/dashboard insights. '
            .'If the user greets or does small talk, reply naturally, then smoothly steer back to useful AquWatch help. '
            .'Avoid making up unavailable data; rely on provided context when giving status updates. '
            .'Keep responses concise, practical, and readable.';

        $contextBlock = [
            'Current context:',
            '- Flood status: '.$context['flood_status'],
            '- Rain status: '.$context['rain_status'],
            '- Flow now (L/min): '.$context['flow_now_lpm'],
            '- Active sensors (last 2 min): '.$context['active_sensors'],
            '- Flood updated: '.$context['flood_updated'],
            '- Rain updated: '.$context['rain_updated'],
            '- Flow updated: '.$context['flow_updated'],
        ];

        $userPrompt = implode("\n", $contextBlock)."\n\nUser question: ".$question;

        try {
            $messages = array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $historyMessages,
                [['role' => 'user', 'content' => $userPrompt]]
            );

            $response = Http::withToken($apiKey)
                ->timeout(25)
                ->acceptJson()
                ->post($baseUrl.'/chat/completions', [
                    'model' => $model,
                    'temperature' => 0.35,
                    'messages' => $messages,
                ]);

            if (! $response->ok()) {
                return null;
            }

            $content = (string) data_get($response->json(), 'choices.0.message.content', '');
            $content = trim($content);

            return $content !== '' ? $content : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, string|int|float>
     */
    private function buildContext(
        string $floodStatus,
        string $rainStatus,
        float $flowNow,
        int $activeSensors,
        ?string $floodAgo,
        ?string $rainAgo,
        ?string $flowAgo
    ): array {
        return [
            'flood_status' => $floodStatus,
            'rain_status' => str_replace('_', ' ', $rainStatus),
            'flow_now_lpm' => round($flowNow, 3),
            'active_sensors' => $activeSensors,
            'flood_updated' => $floodAgo ?? 'not available',
            'rain_updated' => $rainAgo ?? 'not available',
            'flow_updated' => $flowAgo ?? 'not available',
        ];
    }

    private function buildAnswer(
        string $question,
        string $floodStatus,
        string $rainStatus,
        float $flowNow,
        int $activeSensors,
        ?string $floodAgo,
        ?string $rainAgo,
        ?string $flowAgo
    ): string {
        $question = trim($question);
        $rainLabel = str_replace('_', ' ', $rainStatus);
        $risk = $this->estimateRisk($floodStatus, $rainStatus);
        $wantsAdvice = $this->isAdviceQuestion($question);
        $wantsSummary = $this->isSummaryQuestion($question);
        $isRainScenarioQuestion = $this->isRainScenarioQuestion($question);
        $isFloodScenarioQuestion = $this->isFloodScenarioQuestion($question);

        if (preg_match('/\b(hi|hello|hey|yo|sup|what\'s up|whats up)\b/i', $question) === 1) {
            return 'Hey. I am here and ready. If you want, I can quickly summarize flood, rain, flow, and active sensor status now.';
        }

        if (preg_match('/\b(how are you|how r u|hru|you good|you ok)\b/i', $question) === 1) {
            return 'I am good and watching your AquWatch conditions with you. If you want, I can give a quick risk and action summary right now.';
        }

        if ($wantsSummary) {
            return 'Current snapshot: Flood is '.$floodStatus.', rain is '.$rainLabel.', flow is about '.number_format($flowNow, 3).' L/min, '.$activeSensors.' sensors are active, and estimated risk is '.$risk.'.';
        }

        if ($isRainScenarioQuestion) {
            $current = $rainStatus === 'no_rain'
                ? 'Right now it is not raining, so umbrella is optional at the moment.'
                : 'Right now rain status is '.$rainLabel.', so carry an umbrella or raincoat if you go out.';

            return $current.' If rain starts or gets heavier, keep electronics dry, wear slip-resistant footwear, and avoid flood-prone routes.';
        }

        if ($isFloodScenarioQuestion) {
            return 'If flood signs appear, move important items to higher ground, avoid crossing flooded roads, keep your phone charged, and prepare a small emergency bag. Current flood status is '.$floodStatus.'.';
        }

        if ($wantsAdvice) {
            $actions = $this->recommendedActions($floodStatus, $rainStatus, $activeSensors, $flowNow);

            return 'Based on current conditions (risk: '.$risk.'), here is my advice: '.implode(' ', $actions);
        }

        if (str_contains($question, 'flood')) {
            return 'Latest flood status is '.$floodStatus.'. Last flood update was '.($floodAgo ?? 'not available').'.';
        }

        if (str_contains($question, 'rain')) {
            return 'Latest rain status is '.$rainLabel.'. Last rain update was '.($rainAgo ?? 'not available').'.';
        }

        if (str_contains($question, 'flow')) {
            return 'Current flow is about '.number_format($flowNow, 3).' L/min. Last flow update was '.($flowAgo ?? 'not available').'.';
        }

        if (str_contains($question, 'sensor') || str_contains($question, 'online') || str_contains($question, 'active')) {
            return 'There are '.$activeSensors.' active sensors seen in the last 2 minutes.';
        }

        if (str_contains($question, 'risk') || str_contains($question, 'safe') || str_contains($question, 'danger')) {
            return 'Current estimated risk level is '.$risk.'. Flood: '.$floodStatus.', Rain: '.$rainLabel.'.';
        }

        return 'I did not fully catch that, but I can still help. Ask me things like "give advice now", "is risk high", "flood status", "rain update", or "what should we do next".';
    }

    private function estimateRisk(string $floodStatus, string $rainStatus): string
    {
        if (in_array($floodStatus, ['FLASH FLOOD WARNING', 'CRITICAL'], true) || $rainStatus === 'heavy_rain') {
            return 'high';
        }

        if (in_array($floodStatus, ['NORMAL RISE', 'LEVEL 1 DETECTED'], true) || $rainStatus === 'rain') {
            return 'moderate';
        }

        return 'low';
    }

    /**
     * @return array<int, string>
     */
    private function recommendedActions(string $floodStatus, string $rainStatus, int $activeSensors, float $flowNow): array
    {
        $actions = [];

        if ($rainStatus === 'no_rain') {
            $actions[] = 'Weather is currently calm; normal activity is fine, but keep a compact umbrella ready if clouds build up.';
        }

        if ($rainStatus === 'rain') {
            $actions[] = 'If you are going outside, bring an umbrella or light raincoat.';
            $actions[] = 'Avoid leaving items where they can get wet and slippery.';
        }

        if ($rainStatus === 'heavy_rain') {
            $actions[] = 'Bring full rain protection (umbrella plus raincoat) and avoid unnecessary travel.';
            $actions[] = 'Use safer routes and avoid low-lying areas that can flood quickly.';
        }

        if (in_array($floodStatus, ['FLASH FLOOD WARNING', 'CRITICAL'], true)) {
            $actions[] = 'Trigger high-priority alert broadcast now.';
            $actions[] = 'Confirm evacuation routes and local response contacts are active.';
            $actions[] = 'Move vehicles, equipment, and important items to higher ground immediately.';
        } elseif (in_array($floodStatus, ['NORMAL RISE', 'LEVEL 1 DETECTED'], true)) {
            $actions[] = 'Increase monitoring frequency for the next 30 to 60 minutes.';
            $actions[] = 'Prepare emergency items now in case conditions escalate.';
        }

        if ($rainStatus === 'heavy_rain') {
            $actions[] = 'Expect possible rapid rise; keep flood notifications on standby.';
        } elseif ($rainStatus === 'rain') {
            $actions[] = 'Track trend continuity to see if rain escalates to heavy rain.';
        }

        if ($activeSensors < 3) {
            $actions[] = 'Check stale sensors to avoid blind spots in the dashboard.';
        }

        if ($flowNow > 0 && $flowNow > 8) {
            $actions[] = 'Flow is elevated; inspect intake or drainage path for anomalies.';
        }

        if ($actions === []) {
            $actions[] = 'Conditions are stable; continue normal monitoring and keep notifications enabled.';
        }

        return $actions;
    }

    private function isAdviceQuestion(string $question): bool
    {
        return preg_match(
            '/\b(advice|recommend|what should i do|what do i do|next step|what action|what to prepare|should i|should we|can i go out|go outside|what should i bring|what should i wear|how to prepare|in this environment|in this condition|in this situation)\b/i',
            $question
        ) === 1;
    }

    private function isSummaryQuestion(string $question): bool
    {
        return preg_match(
            '/\b(summary|overall|status now|what now|current condition|current situation|quick update|full update|give me update)\b/i',
            $question
        ) === 1;
    }

    private function isRainScenarioQuestion(string $question): bool
    {
        return preg_match(
            '/\b(if there(?:\'| i)?s rain|if it rains|if raining|when it rains|if heavy rain|if rain comes|if rain starts)\b/i',
            $question
        ) === 1;
    }

    private function isFloodScenarioQuestion(string $question): bool
    {
        return preg_match(
            '/\b(if flood|if flooding|if flash flood|if flood warning|when flood happens|if water rises|if river rises)\b/i',
            $question
        ) === 1;
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    private function getChatHistory(): array
    {
        return collect((array) session(self::HISTORY_SESSION_KEY, []))
            ->filter(function ($item): bool {
                return is_array($item)
                    && in_array(($item['role'] ?? ''), ['user', 'assistant'], true)
                    && is_string($item['content'] ?? null)
                    && trim((string) ($item['content'] ?? '')) !== '';
            })
            ->map(function (array $item): array {
                return [
                    'role' => (string) $item['role'],
                    'content' => trim((string) $item['content']),
                ];
            })
            ->take(self::MAX_HISTORY_MESSAGES)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    private function historyForLlm(): array
    {
        return collect($this->getChatHistory())
            ->slice(-8)
            ->values()
            ->all();
    }

    private function storeChatTurn(string $question, string $answer): void
    {
        $history = collect($this->getChatHistory())
            ->push([
                'role' => 'user',
                'content' => trim($question),
            ])
            ->push([
                'role' => 'assistant',
                'content' => trim($answer),
            ])
            ->slice(-self::MAX_HISTORY_MESSAGES)
            ->values()
            ->all();

        session([self::HISTORY_SESSION_KEY => $history]);
    }
}
