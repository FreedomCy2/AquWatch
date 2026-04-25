<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - AquWatch Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-800">
    <header class="max-w-6xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-3xl font-black text-blue-900">Edit Announcement</h1>
            <p class="text-blue-800/80 text-sm">Update announcement content shown to users</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl bg-white/70 border border-white font-semibold text-blue-900">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 pb-10">
        <div class="bg-white/65 rounded-2xl border border-white shadow p-6 md:p-7">
            <h2 class="text-xl font-bold text-blue-900 mb-5"><i class="fas fa-bullhorn mr-2"></i>Announcement Details</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-blue-900 mb-1">Title</label>
                    <input name="title" value="{{ old('title', $announcement->title) }}" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-blue-900 mb-1">Body</label>
                    <textarea name="body" rows="7" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-400">{{ old('body', $announcement->body) }}</textarea>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-white text-slate-700 font-semibold border border-slate-200">Cancel</a>
                    <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
