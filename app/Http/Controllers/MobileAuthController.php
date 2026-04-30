<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    /**
     * Mobile-app-friendly login.
     * Returns the user id so the Android app can link its FCM token to the user.
     */
    public function login(Request $request): JsonResponse
    {
        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>v</mi><mi>a</mi><mi>l</mi><mi>i</mi><mi>d</mi><mi>a</mi><mi>t</mi><mi>e</mi><mi>d</mi><mo>=</mo></mrow><annotation encoding="application/x-tex">validated = </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.6944em;"></span><span class="mord mathnormal" style="margin-right:0.03588em;">v</span><span class="mord mathnormal">a</span><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="mord mathnormal">i</span><span class="mord mathnormal">d</span><span class="mord mathnormal">a</span><span class="mord mathnormal">t</span><span class="mord mathnormal">e</span><span class="mord mathnormal">d</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span></span></span></span>request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'token'    => ['nullable', 'string', 'max:512'], // optional FCM token
        ]);

        <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>u</mi><mi>s</mi><mi>e</mi><mi>r</mi><mo>=</mo><mi>U</mi><mi>s</mi><mi>e</mi><mi>r</mi><mo>:</mo><mo>:</mo><mi>w</mi><mi>h</mi><mi>e</mi><mi>r</mi><mi>e</mi><msup><mo stretchy="false">(</mo><mo mathvariant="normal" lspace="0em" rspace="0em">′</mo></msup><mi>e</mi><mi>m</mi><mi>a</mi><mi>i</mi><msup><mi>l</mi><mo mathvariant="normal" lspace="0em" rspace="0em">′</mo></msup><mo separator="true">,</mo></mrow><annotation encoding="application/x-tex">user = User::where(&#x27;email&#x27;, </annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.4306em;"></span><span class="mord mathnormal">u</span><span class="mord mathnormal" style="margin-right:0.02778em;">ser</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">=</span><span class="mspace" style="margin-right:0.2778em;"></span></span><span class="base"><span class="strut" style="height:0.6833em;"></span><span class="mord mathnormal" style="margin-right:0.10903em;">U</span><span class="mord mathnormal" style="margin-right:0.02778em;">ser</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">::</span><span class="mspace" style="margin-right:0.2778em;"></span></span><span class="base"><span class="strut" style="height:1.0019em;vertical-align:-0.25em;"></span><span class="mord mathnormal" style="margin-right:0.02691em;">w</span><span class="mord mathnormal">h</span><span class="mord mathnormal">ere</span><span class="mopen"><span class="mopen">(</span><span class="msupsub"><span class="vlist-t"><span class="vlist-r"><span class="vlist" style="height:0.7519em;"><span style="top:-3.063em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mtight">′</span></span></span></span></span></span></span></span></span><span class="mord mathnormal">e</span><span class="mord mathnormal">mai</span><span class="mord"><span class="mord mathnormal" style="margin-right:0.01968em;">l</span><span class="msupsub"><span class="vlist-t"><span class="vlist-r"><span class="vlist" style="height:0.7519em;"><span style="top:-3.063em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mtight">′</span></span></span></span></span></span></span></span></span><span class="mpunct">,</span></span></span></span>validated['email'])->first();

        if (! <span class="katex"><span class="katex-mathml"><math xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><mi>u</mi><mi>s</mi><mi>e</mi><mi>r</mi><mi mathvariant="normal">∣</mi><mi mathvariant="normal">∣</mi><mo stretchy="false">!</mo><mi>H</mi><mi>a</mi><mi>s</mi><mi>h</mi><mo>:</mo><mo>:</mo><mi>c</mi><mi>h</mi><mi>e</mi><mi>c</mi><mi>k</mi><mo stretchy="false">(</mo></mrow><annotation encoding="application/x-tex">user || ! Hash::check(</annotation></semantics></math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathnormal">u</span><span class="mord mathnormal" style="margin-right:0.02778em;">ser</span><span class="mord">∣∣</span><span class="mclose">!</span><span class="mord mathnormal" style="margin-right:0.08125em;">H</span><span class="mord mathnormal">a</span><span class="mord mathnormal">s</span><span class="mord mathnormal">h</span><span class="mspace" style="margin-right:0.2778em;"></span><span class="mrel">::</span><span class="mspace" style="margin-right:0.2778em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathnormal">c</span><span class="mord mathnormal">h</span><span class="mord mathnormal">ec</span><span class="mord mathnormal" style="margin-right:0.03148em;">k</span><span class="mopen">(</span></span></span></span>validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Optionally save/link FCM token right away
        if (! empty($validated['token'])) {
            \App\Models\FcmToken::updateOrCreate(
                ['token' => $validated['token']],
                [
                    'user_id'      => $user->id,
                    'platform'     => 'android',
                    'last_used_at' => now(),
                ]
            );
        }

        return response()->json([
            'message' => 'Logged in.',
            'user_id' => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
        ]);
    }
}
