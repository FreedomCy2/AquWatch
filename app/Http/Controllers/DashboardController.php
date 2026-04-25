<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\FlowReading;
use App\Models\FloodReading;
use App\Models\RainReading;
use App\Models\Sensor;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isAdmin()) {
            return view('dashboard');
        }

        if (! $request->routeIs('admin.*')) {
            return redirect()->route('admin.dashboard');
        }

        $latestFlowPerSensor = FlowReading::query()
            ->select(['sensor_id', 'flow_lpm', 'created_at'])
            ->latest('created_at')
            ->get()
            ->unique('sensor_id')
            ->take(4)
            ->values();

        $liveCutoff = now()->subMinutes(2);

        $recentFlowPerSensor = $latestFlowPerSensor
            ->filter(fn ($reading): bool => $reading->created_at?->greaterThanOrEqualTo($liveCutoff) ?? false)
            ->values();

        $flowLpm = round((float) $recentFlowPerSensor->sum(fn ($reading): float => (float) $reading->flow_lpm), 3);
        $flowSensors = $recentFlowPerSensor
            ->take(2)
            ->map(fn ($reading): array => [
                'sensor_id' => (string) $reading->sensor_id,
                'flow_lpm' => round((float) $reading->flow_lpm, 3),
            ])
            ->values()
            ->all();

        $latestRain = RainReading::query()->latest('created_at')->first();
        $latestFlood = FloodReading::query()->latest('created_at')->first();

        $totalSensors = Sensor::query()->where('is_active', true)->count();
        $activeSensors = Sensor::query()
            ->where('is_active', true)
            ->where('last_seen_at', '>=', now()->subMinutes(2))
            ->count();

        $userSearch = trim((string) $request->query('email', ''));

        $usersQuery = User::query()->latest();

        if ($userSearch !== '') {
            $usersQuery->where('email', 'like', '%'.$userSearch.'%');
        }

        $users = $usersQuery->take(25)->get();

        return view('admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'userCount' => User::query()->where('role', 'user')->count(),
            'adminCount' => User::query()->where('role', 'admin')->count(),
            'activeSensors' => $activeSensors,
            'totalSensors' => $totalSensors,
            'latestRainLabel' => match ((string) ($latestRain?->intensity_level ?? 'no_rain')) {
                'heavy_rain' => 'Heavy Rain',
                'rain' => 'Rain',
                default => 'No Rain',
            },
            'latestFloodLabel' => (string) ($latestFlood?->status ?? 'Safe / Dry'),
            'latestFlowLpm' => $flowLpm,
            'flowSensors' => $flowSensors,
            'users' => $users,
            'userSearch' => $userSearch,
            'notificationRecipients' => User::query()->latest()->take(50)->get(),
            'announcements' => Announcement::query()->latest()->take(8)->get(),
            'adminNotifications' => UserNotification::query()->latest()->take(8)->get(),
        ]);
    }
}
