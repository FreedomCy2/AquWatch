<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const LANGUAGES = [
        'en',
        'ms',
    ];

    private const TIMEZONES = [
        'UTC',
        'Asia/Brunei',
        'Asia/Kuala_Lumpur',
        'Asia/Singapore',
        'Asia/Jakarta',
    ];

    private const FLOOD_TRIGGERS = [
        'LEVEL 1 DETECTED',
        'NORMAL RISE',
        'FLASH FLOOD WARNING',
        'CRITICAL',
    ];

    private const RAIN_TRIGGERS = [
        'rain',
        'heavy_rain',
    ];

    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?: new Profile();
        $preferences = (array) ($profile->preferences ?? []);

        $settings = [
            'preferred_language' => (string) ($profile->preferred_language ?? 'en'),
            'timezone' => (string) ($user->timezone ?: config('app.timezone', 'UTC')),
            'flood_trigger' => (string) ($preferences['flood_trigger'] ?? 'LEVEL 1 DETECTED'),
            'rain_trigger' => (string) ($preferences['rain_trigger'] ?? 'heavy_rain'),
            'flow_anomaly_percent' => (int) ($preferences['flow_anomaly_percent'] ?? 30),
            'quiet_hours_start' => $preferences['quiet_hours_start'] ?? null,
            'quiet_hours_end' => $preferences['quiet_hours_end'] ?? null,
            'escalation_contact' => $preferences['escalation_contact'] ?? null,
            'retention_days' => (int) ($preferences['retention_days'] ?? 90),
        ];

        return view('settings-edit', [
            'user' => $user,
            'settings' => $settings,
            'languages' => self::LANGUAGES,
            'timezones' => self::TIMEZONES,
            'floodTriggers' => self::FLOOD_TRIGGERS,
            'rainTriggers' => self::RAIN_TRIGGERS,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'preferred_language' => ['required', Rule::in(self::LANGUAGES)],
            'timezone' => ['required', Rule::in(self::TIMEZONES)],
            'flood_trigger' => ['required', Rule::in(self::FLOOD_TRIGGERS)],
            'rain_trigger' => ['required', Rule::in(self::RAIN_TRIGGERS)],
            'flow_anomaly_percent' => ['required', 'integer', 'min:5', 'max:200'],
            'quiet_hours_start' => ['nullable', 'date_format:H:i'],
            'quiet_hours_end' => ['nullable', 'date_format:H:i'],
            'escalation_contact' => ['nullable', 'string', 'max:255'],
            'retention_days' => ['required', 'integer', Rule::in([7, 30, 90, 365])],
        ]);

        $user = Auth::user();

        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            ['country' => 'Brunei Darussalam']
        );

        $profile->preferred_language = $data['preferred_language'];
        $user->timezone = $data['timezone'];

        $profile->preferences = [
            'flood_trigger' => $data['flood_trigger'],
            'rain_trigger' => $data['rain_trigger'],
            'flow_anomaly_percent' => (int) $data['flow_anomaly_percent'],
            'quiet_hours_start' => $data['quiet_hours_start'] ?? null,
            'quiet_hours_end' => $data['quiet_hours_end'] ?? null,
            'escalation_contact' => $data['escalation_contact'] ?? null,
            'retention_days' => (int) $data['retention_days'],
        ];

        $profile->save();
        $user->save();

        $request->session()->put('locale', $data['preferred_language']);
        App::setLocale($data['preferred_language']);

        return redirect()->route('account.settings.edit')->with('success', __('ui.settings_updated'));
    }
}
