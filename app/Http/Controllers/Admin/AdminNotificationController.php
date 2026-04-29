<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FirebaseMessagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function store(Request $request, FirebaseMessagingService $firebaseMessaging): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $notification = UserNotification::query()->create([
            'user_id' => $validated['user_id'] ?? null,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'sent_by' => (int) auth()->id(),
        ]);

        if (($validated['user_id'] ?? null) === null) {
            return redirect()->route('admin.dashboard')->with(
                'success',
                'Notification saved for all users. Push notification skipped for broadcast messages.'
            );
        }

        $push = $firebaseMessaging->sendUserNotification($notification);

        if (($push['skipped'] ?? false) === true) {
            $reason = (string) ($push['reason'] ?? 'Push not configured.');

            return redirect()->route('admin.dashboard')
                ->with('success', 'Notification saved. Push skipped: '.$reason);
        }

        return redirect()->route('admin.dashboard')->with(
            'success',
            sprintf(
                'Notification sent. Push attempted: %d, delivered: %d, failed: %d.',
                (int) ($push['attempted'] ?? 0),
                (int) ($push['sent'] ?? 0),
                (int) ($push['failed'] ?? 0)
            )
        );
    }

    public function edit(UserNotification $notification): View
    {
        return view('admin.notifications.edit', [
            'notification' => $notification,
            'recipients' => User::query()->latest()->take(100)->get(),
        ]);
    }

    public function update(Request $request, UserNotification $notification): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $notification->update([
            'user_id' => $validated['user_id'] ?? null,
            'title' => $validated['title'],
            'message' => $validated['message'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Notification updated.');
    }

    public function destroy(UserNotification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Notification deleted.');
    }
}
