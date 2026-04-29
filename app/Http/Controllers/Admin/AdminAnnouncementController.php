<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\UserNotification;
use App\Services\FirebaseMessagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function store(Request $request, FirebaseMessagingService $firebaseMessaging): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $announcement = Announcement::query()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'created_by' => (int) auth()->id(),
            'is_active' => true,
            'published_at' => now(),
        ]);

        $notification = UserNotification::query()->create([
            'user_id' => null,
            'title' => $announcement->title,
            'message' => $announcement->body,
            'sent_by' => (int) auth()->id(),
        ]);

        $firebaseMessaging->sendUserNotification($notification);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement published.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $announcement->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Announcement deleted.');
    }
}
