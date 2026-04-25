<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        UserNotification::query()->create([
            'user_id' => $validated['user_id'] ?? null,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'sent_by' => (int) auth()->id(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Notification sent.');
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
