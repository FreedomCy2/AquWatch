<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['nullable', 'string', 'in:web,android,ios'],
        ]);

        $token = FcmToken::query()->updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => $request->user()?->id,
                'platform' => $validated['platform'] ?? 'web',
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Token saved.',
            'id' => $token->id,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        FcmToken::query()->where('token', $validated['token'])->delete();

        return response()->json([
            'message' => 'Token removed.',
        ]);
    }
}
