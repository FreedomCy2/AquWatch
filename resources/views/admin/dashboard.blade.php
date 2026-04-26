<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquWatch Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen overflow-x-hidden bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-800">
    <header class="max-w-6xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-blue-900 leading-tight">AquWatch Admin <span class="inline-flex whitespace-nowrap text-xs sm:text-sm bg-rose-400 text-white px-2 py-1 rounded-full align-middle mt-1 sm:mt-0">Admin Panel</span></h1>
            <p class="text-blue-800/80 text-sm">System Control Center</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl bg-white/70 border border-white font-semibold text-blue-900">
                <i class="fas fa-user-shield mr-1"></i> Admin
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-2 rounded-xl bg-white/70 border border-white font-semibold text-blue-900" type="submit">
                    <i class="fas fa-right-from-bracket mr-1"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 pb-28 md:pb-10 space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
        @endif

        <section id="admin-stats" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/65 rounded-2xl p-5 border border-white shadow">
                <p class="text-sm text-blue-700">Total Registered Accounts</p>
                <p class="text-3xl font-black text-blue-900">{{ $totalUsers }}</p>
                <p class="text-xs text-slate-600">Users: {{ $userCount }} | Admins: {{ $adminCount }}</p>
            </div>
            <div class="bg-white/65 rounded-2xl p-5 border border-white shadow">
                <p class="text-sm text-blue-700">Active Sensors</p>
                <p class="text-3xl font-black text-blue-900">{{ $activeSensors }}</p>
                <p class="text-xs text-slate-600">Online now out of {{ $totalSensors }} configured sensors</p>
            </div>
            <div class="bg-white/65 rounded-2xl p-5 border border-white shadow">
                <p class="text-sm text-blue-700">Total Announcements</p>
                <p class="text-3xl font-black text-blue-900">{{ $announcements->count() }}</p>
                <p class="text-xs text-slate-600">Recent communications from admin</p>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('contents.rain-display') }}" class="bg-white/65 rounded-2xl p-5 border border-white shadow hover:bg-white/80 transition block">
                <p class="text-sm text-blue-700"><i class="fas fa-cloud-rain mr-1"></i> Rain Display</p>
                <p id="admin-rain-status" class="text-2xl font-bold text-blue-900">{{ $latestRainLabel }}</p>
                <p id="admin-rain-meta" class="text-xs text-slate-700 mt-1">Waiting for latest rain sample...</p>
            </a>
            <a href="{{ route('contents.flood-display') }}" class="bg-white/65 rounded-2xl p-5 border border-white shadow hover:bg-white/80 transition block">
                <p class="text-sm text-blue-700"><i class="fas fa-water mr-1"></i> Flood Display</p>
                <p id="admin-flood-status" class="text-2xl font-bold text-blue-900">{{ $latestFloodLabel }}</p>
                <p id="admin-flood-meta" class="text-xs text-slate-700 mt-1">Waiting for latest flood sample...</p>
            </a>
            <a href="{{ route('contents.flow-display') }}" class="bg-white/65 rounded-2xl p-5 border border-white shadow hover:bg-white/80 transition block">
                <p class="text-sm text-blue-700"><i class="fas fa-tint mr-1"></i> Flow Display</p>
                <p id="admin-flow-combined" class="text-2xl font-bold text-blue-900">{{ number_format($latestFlowLpm, 3) }} L/min</p>
                <p id="admin-flow-s1" class="text-xs text-slate-700 mt-1">
                    S1: {{ number_format((float) ($flowSensors[0]['flow_lpm'] ?? 0), 3) }} L/min
                    @if (!empty($flowSensors[0]['sensor_id']))
                        ({{ $flowSensors[0]['sensor_id'] }})
                    @endif
                </p>
                <p id="admin-flow-s2" class="text-xs text-slate-700">
                    S2: {{ number_format((float) ($flowSensors[1]['flow_lpm'] ?? 0), 3) }} L/min
                    @if (!empty($flowSensors[1]['sensor_id']))
                        ({{ $flowSensors[1]['sensor_id'] }})
                    @endif
                </p>
            </a>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div id="admin-users" class="lg:col-span-2 bg-white/65 rounded-2xl border border-white shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-blue-900"><i class="fas fa-users mr-1"></i> Registered Users</h2>
                        <span class="text-xs text-slate-600">Manage role, edit, delete</span>
                    </div>

                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <input
                            type="text"
                            name="email"
                            value="{{ $userSearch }}"
                            placeholder="Search email..."
                            class="w-52 md:w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white"
                        >
                        <button type="submit" class="px-3 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold">Search</button>
                        @if ($userSearch !== '')
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Clear</a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-sky-50 text-blue-900">
                            <tr>
                                <th class="px-4 py-2 text-left">User</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Role</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $managedUser)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-2">{{ $managedUser->name }}</td>
                                    <td class="px-4 py-2">{{ $managedUser->email }}</td>
                                    <td class="px-4 py-2 uppercase text-xs font-semibold">{{ $managedUser->role ?: 'user' }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <a href="{{ route('admin.users.edit', $managedUser) }}" class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-red-100 text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-4">
                <form id="admin-announcements" method="POST" action="{{ route('admin.announcements.store') }}" class="bg-white/65 rounded-2xl border border-white shadow p-4 space-y-3">
                    @csrf
                    <h2 class="font-bold text-blue-900"><i class="fas fa-bullhorn mr-1"></i> Publish Announcement</h2>
                    <input name="title" value="{{ old('title') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Announcement title" required>
                    <textarea name="body" class="w-full rounded-xl border border-slate-200 px-3 py-2" rows="3" placeholder="Write announcement..." required>{{ old('body') }}</textarea>
                    <button type="submit" class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2">Post</button>
                </form>

                <div class="bg-white/65 rounded-2xl border border-white shadow p-4 space-y-3">
                    <h2 class="font-bold text-blue-900"><i class="fas fa-list mr-1"></i>Recent Announcements</h2>
                    @forelse ($announcements as $announcement)
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="font-semibold text-slate-800">{{ $announcement->title }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $announcement->created_at?->diffForHumans() }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-lg bg-red-100 text-red-700 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No announcements yet.</p>
                    @endforelse
                </div>

                <form id="admin-notifications" method="POST" action="{{ route('admin.notifications.store') }}" class="bg-white/65 rounded-2xl border border-white shadow p-4 space-y-3">
                    @csrf
                    <h2 class="font-bold text-blue-900"><i class="fas fa-bell mr-1"></i> Send Notification</h2>
                    <select name="user_id" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="">All Users</option>
                        @foreach ($notificationRecipients as $managedUser)
                            <option value="{{ $managedUser->id }}">{{ $managedUser->name }} ({{ $managedUser->email }})</option>
                        @endforeach
                    </select>
                    <input name="title" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Notification title" required>
                    <textarea name="message" class="w-full rounded-xl border border-slate-200 px-3 py-2" rows="3" placeholder="Notification message" required></textarea>
                    <button type="submit" class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2">Send</button>
                </form>

                <div class="bg-white/65 rounded-2xl border border-white shadow p-4 space-y-3">
                    <h2 class="font-bold text-blue-900"><i class="fas fa-envelope mr-1"></i>Recent Notifications</h2>
                    @forelse ($adminNotifications as $notification)
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <p class="font-semibold text-slate-800">{{ $notification->title }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <a href="{{ route('admin.notifications.edit', $notification) }}" class="px-2 py-1 rounded-lg bg-amber-100 text-amber-800 text-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded-lg bg-red-100 text-red-700 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No notifications yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <nav class="fixed bottom-0 inset-x-0 z-40 md:hidden bg-white/90 backdrop-blur-md border-t border-white/80 shadow-[0_-6px_18px_rgba(20,80,120,0.12)] pb-[env(safe-area-inset-bottom)]">
        <div class="grid grid-cols-5 gap-1 px-2 py-2 text-[11px] font-semibold text-blue-900">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 bg-sky-100/80">
                <i class="fas fa-house text-sm"></i>
                <span>Home</span>
            </a>
            <a href="#admin-users" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-users text-sm"></i>
                <span>Users</span>
            </a>
            <a href="#admin-announcements" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-bullhorn text-sm"></i>
                <span>Post</span>
            </a>
            <a href="#admin-notifications" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-bell text-sm"></i>
                <span>Notify</span>
            </a>
            <a href="{{ route('contents.rain-display') }}" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-wave-square text-sm"></i>
                <span>Sensors</span>
            </a>
        </div>
    </nav>

    <script>
        const adminFlowUrl = @json(route('contents.flow-display.data'));
        const adminRainUrl = @json(route('contents.rain-display.data'));
        const adminFloodUrl = @json(route('contents.flood-display.data'));

        function rainLabel(level) {
            if (level === 'heavy_rain') return 'Heavy Rain';
            if (level === 'rain') return 'Rain';
            return 'No Rain';
        }

        function floodLabel(status) {
            if (status === 'CRITICAL') return 'Critical';
            if (status === 'FLASH FLOOD WARNING') return 'Flash Flood Warning';
            if (status === 'NORMAL RISE') return 'Normal Rise';
            if (status === 'LEVEL 1 DETECTED') return 'Level 1 Detected';
            return 'Safe / Dry';
        }

        function updateAdminRainCard(payload) {
            const latest = payload?.latest ?? null;
            const statusEl = document.getElementById('admin-rain-status');
            const metaEl = document.getElementById('admin-rain-meta');

            if (!statusEl || !metaEl) {
                return;
            }

            if (!latest) {
                statusEl.textContent = 'No Rain';
                metaEl.textContent = 'No readings yet';
                return;
            }

            statusEl.textContent = rainLabel(String(latest.intensity_level ?? 'no_rain'));
            const recency = latest.is_recent ? 'Live' : 'Stale';
            metaEl.textContent = `Sensor: ${latest.sensor_id ?? '-'} | Value: ${Number(latest.analog_value ?? 0)} | ${recency}`;
        }

        function updateAdminFloodCard(payload) {
            const latest = payload?.latest ?? null;
            const statusEl = document.getElementById('admin-flood-status');
            const metaEl = document.getElementById('admin-flood-meta');

            if (!statusEl || !metaEl) {
                return;
            }

            if (!latest) {
                statusEl.textContent = 'Safe / Dry';
                metaEl.textContent = 'No readings yet';
                return;
            }

            statusEl.textContent = floodLabel(String(latest.status ?? 'SAFE / DRY'));
            const recency = latest.is_recent ? 'Live' : 'Stale';
            metaEl.textContent = `Sensor: ${latest.sensor_id ?? '-'} | Rise: ${Number(latest.rise_time_sec ?? 0)}s | ${recency}`;
        }

        function updateAdminFlowCard(payload) {
            const sensors = Array.isArray(payload?.sensors) ? payload.sensors : [];
            const combined = Number(payload?.combined?.flow_lpm ?? 0);

            const s1 = sensors[0] ?? null;
            const s2 = sensors[1] ?? null;

            const combinedEl = document.getElementById('admin-flow-combined');
            const s1El = document.getElementById('admin-flow-s1');
            const s2El = document.getElementById('admin-flow-s2');

            if (combinedEl) {
                combinedEl.textContent = combined.toFixed(3) + ' L/min';
            }

            if (s1El) {
                const value = Number(s1?.flow_lpm ?? 0).toFixed(3);
                const sensorId = s1?.sensor_id ? ` (${s1.sensor_id})` : '';
                s1El.textContent = `S1: ${value} L/min${sensorId}`;
            }

            if (s2El) {
                const value = Number(s2?.flow_lpm ?? 0).toFixed(3);
                const sensorId = s2?.sensor_id ? ` (${s2.sensor_id})` : '';
                s2El.textContent = `S2: ${value} L/min${sensorId}`;
            }
        }

        async function refreshAdminFlowCard() {
            try {
                const response = await fetch(adminFlowUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                updateAdminFlowCard(payload);
            } catch {
                // Keep last shown values on transient failures.
            }
        }

        async function refreshAdminRainCard() {
            try {
                const response = await fetch(adminRainUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                updateAdminRainCard(payload);
            } catch {
                // Keep last shown values on transient failures.
            }
        }

        async function refreshAdminFloodCard() {
            try {
                const response = await fetch(adminFloodUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                updateAdminFloodCard(payload);
            } catch {
                // Keep last shown values on transient failures.
            }
        }

        refreshAdminRainCard();
        refreshAdminFloodCard();
        refreshAdminFlowCard();
        setInterval(refreshAdminRainCard, 4000);
        setInterval(refreshAdminFloodCard, 4000);
        setInterval(refreshAdminFlowCard, 4000);
    </script>
</body>
</html>
