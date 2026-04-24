<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('plans', [
            'currentPlan' => (string) (auth()->user()?->plan_tier ?? 'free'),
        ]);
    }

    public function switchPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_tier' => ['required', 'string', 'in:free,pro'],
        ]);

        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $targetPlan = (string) $validated['plan_tier'];
        $currentPlan = strtolower((string) $user->plan_tier ?: 'free');

        if ($currentPlan === $targetPlan) {
            return redirect()->route('plans')
                ->with('success', 'Your plan is already set to '.strtoupper($targetPlan).'.');
        }

        $user->forceFill([
            'plan_tier' => $targetPlan,
            'plan_changed_at' => now(),
        ])->save();

        if ($targetPlan === 'pro') {
            return redirect()->route('dashboard')
                ->with('success', 'Pro plan activated. Your dashboard is now unlocked with Pro features.');
        }

        return redirect()->route('plans')
            ->with('success', 'You are now on the Free plan. Pro-only AI insights are disabled.');
    }
}
