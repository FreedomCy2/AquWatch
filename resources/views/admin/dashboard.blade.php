<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        <!-- Push notification enable banner -->
        <div id="fcm-banner" class="hidden rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 flex items-center justify-between gap-3">
            <div>
                <p class="font-semibold"><i class="fas fa-bell mr-1"></i> Enable push notifications</p>
                <p class="text-xs">Click "Enable" so this device can receive alerts. Required for admin pushes to reach you.</p>
            </div>
            <button id="fcm-enable-btn" type="button" class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm">Enable</button>
        </div>

        <section id="admin-stats" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/65 rounded-2xl p-5 border border-white shadow">
                <p class="text-sm text-blue-700">Total Registered Accounts</p>
                <p class="text-3xl font-black text-blue-900">{{ $totalUsers }}</p>
                <p class="text-xs text-slate-600">Users: {{ $userCount }} | Admins: {{ $adminCount }}</p>
            </div>
            <div class="bg-white/65 rounded-2xl p-5 border border-white shadow">
                <p class="text-sm text-blue-700">Active Sensors</p>
                <p id="admin-active-sensors" class="text-3xl font-black text-blue-900">{{ $activeSensors }}</p>
                <p class="text-xs text-slate-600">Online now out of <span id="admin-total-sensors">{{ $totalSensors }}</span> configured sensors</p>
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
                        <input type="text" name="email" value="{{ $userSearch }}" placeholder="Search email..." class="w-52 md:w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white">
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
                <i class="fas fa-house text-sm"></i><span>Home</span>
            </a>
            <a href="#admin-users" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-users text-sm"></i><span>Users</span>
            </a>
            <a href="#admin-announcements" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-bullhorn text-sm"></i><span>Post</span>
            </a>
            <a href="#admin-notifications" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-bell text-sm"></i><span>Notify</span>
            </a>
            <a href="{{ route('contents.rain-display') }}" class="flex flex-col items-center justify-center gap-1 rounded-xl py-2 hover:bg-sky-100/70 transition">
                <i class="fas fa-wave-square text-sm"></i><span>Sensors</span>
            </a>
        </div>
    </nav>

    {{-- ============ FCM push registration ============ --}}
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
        import { getMessaging, getToken, onMessage, isSupported }
            from "https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: @json(env('FIREBASE_WEB_API_KEY')),
            authDomain: @json(env('FIREBASE_WEB_AUTH_DOMAIN')),
            projectId: @json(env('FIREBASE_WEB_PROJECT_ID')),
            storageBucket: @json(env('FIREBASE_WEB_STORAGE_BUCKET')),
            messagingSenderId: @json(env('FIREBASE_WEB_MESSAGING_SENDER_ID')),
            appId: @json(env('FIREBASE_WEB_APP_ID')),
        };
        const vapidKey = @json(env('FIREBASE_WEB_VAPID_KEY'));
        const tokenUrl = @json(route('fcm-token.store'));
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        const banner = document.getElementById('fcm-banner');
        const enableBtn = document.getElementById('fcm-enable-btn');

        async function registerFcm(interactive = false) {
            try {
                if (!(await isSupported())) {
                    console.warn('[FCM] Browser not supported');
                    return;
                }
                if (!('serviceWorker' in navigator)) return;

                if (Notification.permission === 'denied') {
                    console.warn('[FCM] Permission denied by user');
                    return;
                }

                if (Notification.permission === 'default') {
                    if (!interactive) {
                        banner?.classList.remove('hidden');
                        return;
                    }
                    const perm = await Notification.requestPermission();
                    if (perm !== 'granted') return;
                }

                const app = initializeApp(firebaseConfig);
                const messaging = getMessaging(app);
                const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

                const token = await getToken(messaging, {
                    vapidKey: vapidKey,
                    serviceWorkerRegistration: reg,
                });

                if (!token) {
                    console.warn('[FCM] No token returned');
                    return;
                }

                const res = await fetch(tokenUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ token, platform: 'web' }),
                });

                if (res.ok) {
                    console.log('[FCM] Token registered');
                    banner?.classList.add('hidden');
                } else {
                    console.error('[FCM] Save failed', res.status, await res.text());
                }

                onMessage(messaging, (payload) => {
                    console.log('[FCM] Foreground message:', payload);
                    if (Notification.permission === 'granted') {
                        new Notification(payload.notification?.title || 'AquWatch', {
                            body: payload.notification?.body || '',
                            icon: '/favicon.ico',
                        });
                    }
                });
            } catch (e) {
                console.error('[FCM] Registration error:', e);
            }
        }

        enableBtn?.addEventListener('click', () => registerFcm(true));
        registerFcm(false);
    </script>

    <script>
        const sensorStatusUrl = @json(route('sensor-status.data'));
        const adminFlowUrl = @json(route('contents.flow-display.data'));
        const adminRainUrl = @json(route('contents.rain-display.data'));
        const adminFloodUrl = @json(route('contents.flood-display.data'));

        async function refreshSensorStatus() {
            try {
                const r = await fetch(sensorStatusUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const p = await r.json();
                document.getElementById('admin-active-sensors').textContent = String(Number(p?.onlineSensors ?? 0));
                document.getElementById('admin-total-sensors').textContent = String(Number(p?.totalSensors ?? 0));
            } catch {}
        }

        function rainLabel(l) { return l === 'heavy_rain' ? 'Heavy Rain' : l === 'rain' ? 'Rain' : 'No Rain'; }
        function floodLabel(s) {
            if (s === 'CRITICAL') return 'Critical';
            if (s === 'FLASH FLOOD WARNING') return 'Flash Flood Warning';
            if (s === 'NORMAL RISE') return 'Normal Rise';
            if (s === 'LEVEL 1 DETECTED') return 'Level 1 Detected';
            return 'Safe / Dry';
        }

        async function refreshAdminRainCard() {
            try {
                const r = await fetch(adminRainUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const p = await r.json(); const l = p?.latest ?? null;
                const s = document.getElementById('admin-rain-status'); const m = document.getElementById('admin-rain-meta');
                if (!l) { s.textContent = 'No Rain'; m.textContent = 'No readings yet'; return; }
                s.textContent = rainLabel(String(l.intensity_level ?? 'no_rain'));
                m.textContent = `Sensor: ${l.sensor_id ?? '-'} | Value: ${Number(l.analog_value ?? 0)} | ${l.is_recent ? 'Live' : 'Stale'}`;
            } catch {}
        }
        async function refreshAdminFloodCard() {
            try {
                const r = await fetch(adminFloodUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const p = await r.json(); const l = p?.latest ?? null;
                const s = document.getElementById('admin-flood-status'); const m = document.getElementById('admin-flood-meta');
                if (!l) { s.textContent = 'Safe / Dry'; m.textContent = 'No readings yet'; return; }
                s.textContent = floodLabel(String(l.status ?? 'SAFE / DRY'));
                m.textContent = `Sensor: ${l.sensor_id ?? '-'} | Rise: ${Number(l.rise_time_sec ?? 0)}s | ${l.is_recent ? 'Live' : 'Stale'}`;
            } catch {}
        }
        async function refreshAdminFlowCard() {
            try {
                const r = await fetch(adminFlowUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const p = await r.json();
                const sensors = Array.isArray(p?.sensors) ? p.sensors : [];
                const combined = Number(p?.combined?.flow_lpm ?? 0);
                const s1 = sensors[0] ?? null, s2 = sensors[1] ?? null;
                document.getElementById('admin-flow-combined').textContent = combined.toFixed(3) + ' L/min';
                document.getElementById('admin-flow-s1').textContent = `S1: ${Number(s1?.flow_lpm ?? 0).toFixed(3)} L/min${s1?.sensor_id ? ` (${s1.sensor_id})` : ''}`;
                document.getElementById('admin-flow-s2').textContent = `S2: ${Number(s2?.flow_lpm ?? 0).toFixed(3)} L/min${s2?.sensor_id ? ` (${s2.sensor_id})` : ''}`;
            } catch {}
        }

        refreshAdminRainCard(); refreshAdminFloodCard(); refreshAdminFlowCard(); refreshSensorStatus();
        setInterval(refreshAdminRainCard, 4000);
        setInterval(refreshAdminFloodCard, 4000);
        setInterval(refreshAdminFlowCard, 4000);
        setInterval(refreshSensorStatus, 5000);
    </script>
</body>
</html>
