<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseMessagingService
{
    /**
     * @return array{attempted:int,sent:int,failed:int,skipped:bool,reason?:string}
     */
    public function sendUserNotification(UserNotification $notification): array
    {
        $isEnabled = (bool) config('services.firebase.enable_push', false);

        if (! $isEnabled) {
            return [
                'attempted' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => true,
                'reason' => 'Push disabled by FIREBASE_ENABLE_PUSH.',
            ];
        }

        $projectId = (string) config('services.firebase.project_id', '');

        if ($projectId === '') {
            return [
                'attempted' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => true,
                'reason' => 'Missing FIREBASE_PROJECT_ID.',
            ];
        }

        $accessToken = $this->issueAccessToken();

        if ($accessToken === null) {
            return [
                'attempted' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => true,
                'reason' => 'Failed to issue Firebase access token.',
            ];
        }

        $tokensQuery = FcmToken::query()->whereNotNull('token');

        if ($notification->user_id !== null) {
            $tokensQuery->where('user_id', (int) $notification->user_id);
        }

        $tokens = $tokensQuery->pluck('token')->filter()->unique()->values();

        if ($tokens->isEmpty()) {
            return [
                'attempted' => 0,
                'sent' => 0,
                'failed' => 0,
                'skipped' => true,
                'reason' => 'No FCM tokens available.',
            ];
        }

        $url = sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $projectId);

        $attempted = 0;
        $sent = 0;
        $failed = 0;

        foreach ($tokens as $token) {
            $attempted++;

            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->acceptJson()
                ->post($url, [
                    'message' => [
                        'token' => (string) $token,
                        'notification' => [
                            'title' => (string) $notification->title,
                            'body' => (string) $notification->message,
                        ],
                        'data' => [
                            'notification_id' => (string) $notification->id,
                            'type' => 'user_notification',
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $sent++;
                continue;
            }

            $failed++;

            if ($this->responseHasInvalidToken($response->json())) {
                FcmToken::query()->where('token', (string) $token)->delete();
            }

            Log::warning('FCM send failed.', [
                'status' => $response->status(),
                'token' => substr((string) $token, 0, 16).'...',
                'body' => $response->json() ?? $response->body(),
            ]);
        }

        return [
            'attempted' => $attempted,
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => false,
        ];
    }

    private function issueAccessToken(): ?string
    {
        $serviceAccount = $this->loadServiceAccount();

        if (! $serviceAccount) {
            return null;
        }

        $privateKey = (string) ($serviceAccount['private_key'] ?? '');
        $clientEmail = (string) ($serviceAccount['client_email'] ?? '');

        if ($privateKey === '' || $clientEmail === '') {
            return null;
        }

        $now = time();

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_THROW_ON_ERROR));

        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_THROW_ON_ERROR));

        $unsignedToken = $header.'.'.$claims;
        $signature = '';

        $signResult = openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (! $signResult) {
            return null;
        }

        $jwt = $unsignedToken.'.'.$this->base64UrlEncode($signature);

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(10)
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (! $response->successful()) {
            Log::warning('Google OAuth token request failed.', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            return null;
        }

        return (string) ($response->json('access_token') ?? '');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadServiceAccount(): ?array
    {
        $configuredPath = (string) config('services.firebase.service_account_path', '');

        if ($configuredPath === '') {
            return null;
        }

        $path = str_starts_with($configuredPath, DIRECTORY_SEPARATOR)
            ? $configuredPath
            : base_path($configuredPath);

        if (! is_file($path)) {
            Log::warning('Firebase service account file not found.', ['path' => $path]);

            return null;
        }

        $json = file_get_contents($path);

        if (! is_string($json) || $json === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param mixed $payload
     */
    private function responseHasInvalidToken($payload): bool
    {
        $json = json_encode($payload);

        if (! is_string($json)) {
            return false;
        }

        return str_contains($json, 'UNREGISTERED') || str_contains($json, 'INVALID_ARGUMENT');
    }

    private function base64UrlEncode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
