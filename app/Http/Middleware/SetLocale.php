<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        if ($request->user()?->profile?->preferred_language) {
            $locale = $request->user()->profile->preferred_language;
        } elseif ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        if (in_array($locale, ['en', 'ms'], true)) {
            App::setLocale($locale);
            Carbon::setLocale($locale);
            Date::setLocale($locale);
        }

        return $next($request);
    }
}
