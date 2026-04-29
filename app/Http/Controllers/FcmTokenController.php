<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Existing endpoint (web / authenticated clients).
     */
    public function store(Request $request): JsonResponse
    {
        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>v</mi><mi>a</mi><mi>l</mi><mi>i</mi><mi>d</mi><mi>a</mi><mi>t</mi><mi>e</mi><mi>d</mi><mo>=</mo></mrow><annotation encoding="application/x-tex">validated = </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.6944em;"></span><span class="mord mathnormal" style="margin-right:0.03588em;">v</span><span class="mord mathnormal">a</span><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="mord mathnormal">i</span><span class="mord mathnormal">d</span><span class="mord mathnormal">a</span><span class="mord mathnormal">t</span><span class="mord mathnormal">e</span><span class="mord mathnormal">d</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span></span></span></span>request->validate([
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
        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>v</mi><mi>a</mi><mi>l</mi><mi>i</mi><mi>d</mi><mi>a</mi><mi>t</mi><mi>e</mi><mi>d</mi><mo>=</mo></mrow><annotation encoding="application/x-tex">validated = </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.6944em;"></span><span class="mord mathnormal" style="margin-right:0.03588em;">v</span><span class="mord mathnormal">a</span><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="mord mathnormal">i</span><span class="mord mathnormal">d</span><span class="mord mathnormal">a</span><span class="mord mathnormal">t</span><span class="mord mathnormal">e</span><span class="mord mathnormal">d</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span></span></span></span>request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        FcmToken::query()->where('token', $validated['token'])->delete();

        return response()->json([
            'message' => 'Token removed.',
        ]);
    }

    /**
     * NEW: Android app endpoint.
     * - Does NOT require auth (the app can register before login).
     * - Accepts optional user_id so we can link the device after login.
     * - Forces platform = android.
     */
    public function storeFromApp(Request $request): JsonResponse
    {
        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>v</mi><mi>a</mi><mi>l</mi><mi>i</mi><mi>d</mi><mi>a</mi><mi>t</mi><mi>e</mi><mi>d</mi><mo>=</mo></mrow><annotation encoding="application/x-tex">validated = </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.6944em;"></span><span class="mord mathnormal" style="margin-right:0.03588em;">v</span><span class="mord mathnormal">a</span><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="mord mathnormal">i</span><span class="mord mathnormal">d</span><span class="mord mathnormal">a</span><span class="mord mathnormal">t</span><span class="mord mathnormal">e</span><span class="mord mathnormal">d</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span></span></span></span>request->validate([
            'token'   => ['required', 'string', 'max:512'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $token = FcmToken::query()->updateOrCreate(
            ['token' => $validated['token']],
            [
                // Preserve existing user_id if this token already belonged to a user
                // and the current request didn't send one.
                'user_id' => $validated['user_id']
                    ?? optional(FcmToken::where('token', $validated['token'])->first())->user_id,
                'platform' => 'android',
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Device token saved.',
            'id' => $token->id,
            'linked_user_id' => $token->user_id,
        ]);
    }

    /**
     * NEW: Android app endpoint to remove a device token (e.g. on logout / uninstall).
     */
    public function destroyFromApp(Request $request): JsonResponse
    {
        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>v</mi><mi>a</mi><mi>l</mi><mi>i</mi><mi>d</mi><mi>a</mi><mi>t</mi><mi>e</mi><mi>d</mi><mo>=</mo></mrow><annotation encoding="application/x-tex">validated = </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.6944em;"></span><span class="mord mathnormal" style="margin-right:0.03588em;">v</span><span class="mord mathnormal">a</span><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="mord mathnormal">i</span><span class="mord mathnormal">d</span><span class="mord mathnormal">a</span><span class="mord mathnormal">t</span><span class="mord mathnormal">e</span><span class="mord mathnormal">d</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span></span></span></span>request->validate([
            'token' => ['required', 'string', 'max:512'],
        ]);

        FcmToken::query()->where('token', $validated['token'])->delete();

        return response()->json([
            'message' => 'Device token removed.',
        ]);
    }
}
