<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\UserNotification;
use Illuminate\Contracts\View\View;

class UserAnnouncementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $recentNotificationCutoff = now()->subDay();

        $announcements = Announcement::query()
            ->where('is_active', true)
            ->latest('published_at')
            ->latest('created_at')
            ->get();

        $notifications = UserNotification::query()
            ->where(function ($query) use ($user): void {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->where('created_at', '>=', $recentNotificationCutoff)
            ->latest()
            ->get();

        UserNotification::query()
            ->whereNull('read_at')
            ->where(function ($query) use ($user): void {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->update(['read_at' => now()]);

        return view('contents.announcements', [
            'announcements' => $announcements,
            'notifications' => $notifications,
        ]);
    }
}
