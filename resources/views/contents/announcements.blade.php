<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - AquWatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200">
    <main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-black text-blue-900">Announcements</h1>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-white/80 border border-white text-blue-900 font-semibold">Back to Dashboard</a>
        </div>

        <section class="bg-white/70 rounded-2xl border border-white shadow p-5">
            <h2 class="text-xl font-bold text-blue-900 mb-4"><i class="fas fa-bullhorn mr-2"></i>Announcements</h2>
            <div class="space-y-3">
                @forelse ($announcements as $announcement)
                    <article class="rounded-xl border border-slate-200 bg-white p-4">
                        <h3 class="font-bold text-slate-900">{{ $announcement->title }}</h3>
                        <p class="text-sm text-slate-700 mt-2">{{ $announcement->body }}</p>
                        <p class="text-xs text-slate-500 mt-3">Published {{ optional($announcement->published_at ?? $announcement->created_at)->diffForHumans() }}</p>
                    </article>
                @empty
                    <p class="text-slate-600">No announcements yet.</p>
                @endforelse
            </div>
        </section>

        <section class="bg-white/70 rounded-2xl border border-white shadow p-5">
            <h2 class="text-xl font-bold text-blue-900 mb-4"><i class="fas fa-bell mr-2"></i>Notifications</h2>
            <div class="space-y-3">
                @forelse ($notifications as $notification)
                    <article class="rounded-xl border border-slate-200 bg-white p-4">
                        <h3 class="font-bold text-slate-900">{{ $notification->title }}</h3>
                        <p class="text-sm text-slate-700 mt-2">{{ $notification->message }}</p>
                        <p class="text-xs text-slate-500 mt-3">Sent {{ $notification->created_at?->diffForHumans() }}</p>
                    </article>
                @empty
                    <p class="text-slate-600">No notifications from admin yet.</p>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
