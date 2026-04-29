<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - AquWatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .wave-bg {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            pointer-events: none;
            z-index: 1;
        }

        .wave-svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 100px;
            animation: gentleWave 8s ease-in-out infinite alternate;
        }

        .bubble {
            position: fixed;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(255, 255, 245, 0.55);
            pointer-events: none;
            z-index: 2;
            animation: floatUp linear infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.34);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.55);
        }

        .card-enter {
            animation: cardEnter 0.45s ease-out both;
        }

        @keyframes gentleWave {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-15px) translateY(3px); }
        }

        @keyframes floatUp {
            0% { transform: translateY(0); opacity: 0.75; }
            100% { transform: translateY(-100vh); opacity: 0; }
        }

        @keyframes cardEnter {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-900 relative">
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <div class="wave-bg">
        <svg class="wave-svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#4fc3f7" fill-opacity="0.65" d="M0,256L60,250.7C120,245,240,235,360,229.3C480,224,600,224,720,229.3C840,235,960,245,1080,245.3C1200,245,1320,235,1380,229.3L1440,224L1440,320L0,320Z"></path>
        </svg>
    </div>

    <main class="relative z-10 max-w-6xl mx-auto px-4 py-6 md:px-6 md:py-8 space-y-6 pb-12">
        <section class="glass-card rounded-3xl p-5 md:p-7 shadow-xl card-enter">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-widest text-blue-800/75">{{ __('ui.community_center') }}</p>
                    <h1 class="text-3xl md:text-4xl font-black text-cyan-950 leading-tight mt-1">{{ __('ui.announcements') }}</h1>
                    <p class="text-cyan-900/80 mt-2 max-w-2xl">Stay updated with important messages from admins and recent account notifications in one clean timeline.</p>
                </div>

                <a href="{{ route('dashboard') }}" class="inline-flex w-fit max-w-full items-center gap-2 px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition font-semibold break-words">
                    <i class="fas fa-house"></i>
                    <span>{{ __('ui.back_to_dashboard') }}</span>
                </a>
            </div>
        </section>

        @php
            $visibleNotifications = $notifications->take(4);
            $hiddenNotifications = $notifications->slice(4)->values();
        @endphp

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="glass-card rounded-2xl p-4 shadow-lg card-enter">
                <p class="text-xs uppercase tracking-wide text-cyan-900/70">{{ __('ui.active_announcements') }}</p>
                <p class="text-3xl font-black text-cyan-950 mt-1">{{ number_format($announcements->count()) }}</p>
                <p class="text-sm text-cyan-900/75 mt-1">{{ __('ui.published_by_admin') }}</p>
            </div>

            <div class="glass-card rounded-2xl p-4 shadow-lg card-enter" style="animation-delay: 0.08s;">
                <p class="text-xs uppercase tracking-wide text-cyan-900/70">{{ __('ui.admin_notifications') }}</p>
                <p class="text-3xl font-black text-cyan-950 mt-1">{{ number_format($notifications->count()) }}</p>
                <p class="text-sm text-cyan-900/75 mt-1">{{ __('ui.direct_user_updates') }}</p>
            </div>
        </section>

        <section class="glass-card rounded-3xl border border-white/60 shadow-xl p-5 md:p-6 card-enter" style="animation-delay: 0.12s;">
            <div class="mb-4 flex items-center gap-2">
                <span class="w-10 h-10 rounded-xl bg-white/65 border border-white/60 flex items-center justify-center text-orange-500">
                    <i class="fas fa-bullhorn"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-cyan-950">{{ __('ui.announcements') }}</h2>
                    <p class="text-sm text-cyan-900/75">Official posts and updates from admin</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse ($announcements as $announcement)
                    <article class="rounded-2xl border border-white/70 bg-white/75 p-4 md:p-5 shadow-sm hover:shadow-md hover:bg-white/85 transition">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $announcement->title }}</h3>
                            <span class="shrink-0 rounded-full bg-orange-100 text-orange-800 text-[11px] font-semibold px-2 py-1">ADMIN</span>
                        </div>
                        <p class="text-sm md:text-[15px] text-slate-700 mt-2 leading-relaxed">{{ $announcement->body }}</p>
                        <p class="text-xs text-slate-500 mt-3">Published {{ optional($announcement->published_at ?? $announcement->created_at)->diffForHumans() }}</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-cyan-300 bg-cyan-50/60 p-6 text-center text-cyan-900">
                        <i class="fas fa-circle-info mr-1"></i>
                        No announcements yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="glass-card rounded-3xl border border-white/60 shadow-xl p-5 md:p-6 card-enter" style="animation-delay: 0.18s;">
            <div class="mb-4 flex items-center gap-2">
                <span class="w-10 h-10 rounded-xl bg-white/65 border border-white/60 flex items-center justify-center text-cyan-700">
                    <i class="fas fa-bell"></i>
                </span>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-cyan-950">{{ __('ui.my_notifications') }}</h2>
                    <p class="text-sm text-cyan-900/75">Messages sent by admin to you</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse ($visibleNotifications as $notification)
                    <article class="rounded-2xl border border-white/70 bg-white/75 p-4 md:p-5 shadow-sm hover:shadow-md hover:bg-white/85 transition">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $notification->title }}</h3>
                            <span class="shrink-0 rounded-full bg-cyan-100 text-cyan-800 text-[11px] font-semibold px-2 py-1">NOTICE</span>
                        </div>
                        <p class="text-sm md:text-[15px] text-slate-700 mt-2 leading-relaxed">{{ $notification->message }}</p>
                        <p class="text-xs text-slate-500 mt-3">Sent {{ $notification->created_at?->diffForHumans() }}</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-cyan-300 bg-cyan-50/60 p-6 text-center text-cyan-900">
                        <i class="fas fa-circle-info mr-1"></i>
                        No notifications from admin yet.
                    </div>
                @endforelse

                @if ($hiddenNotifications->isNotEmpty())
                    <div id="hidden-notifications" class="hidden space-y-3 pt-1">
                        @foreach ($hiddenNotifications as $notification)
                            <article class="rounded-2xl border border-white/70 bg-white/65 p-4 md:p-5 shadow-sm hover:shadow-md hover:bg-white/85 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $notification->title }}</h3>
                                    <span class="shrink-0 rounded-full bg-cyan-100 text-cyan-800 text-[11px] font-semibold px-2 py-1">NOTICE</span>
                                </div>
                                <p class="text-sm md:text-[15px] text-slate-700 mt-2 leading-relaxed">{{ $notification->message }}</p>
                                <p class="text-xs text-slate-500 mt-3">Sent {{ $notification->created_at?->diffForHumans() }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        <button
                            type="button"
                            id="toggle-notifications"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/80 px-4 py-2 text-cyan-900 font-semibold hover:bg-white transition"
                            aria-expanded="false"
                            aria-controls="hidden-notifications"
                        >
                            <span>{{ __('ui.show_more') }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <footer class="relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="https://x.com/AquWatch" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/aquwatch/" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition"><i class="fab fa-instagram"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch - Protecting our waters
        </p>
    </footer>

    <script>
        const toggleButton = document.getElementById('toggle-notifications');
        const hiddenNotifications = document.getElementById('hidden-notifications');

        if (toggleButton && hiddenNotifications) {
            toggleButton.addEventListener('click', () => {
                const isHidden = hiddenNotifications.classList.toggle('hidden');
                const label = toggleButton.querySelector('span');
                const icon = toggleButton.querySelector('i');

                toggleButton.setAttribute('aria-expanded', String(!isHidden));
                if (label) {
                    label.textContent = isHidden ? @json(__('ui.show_more')) : @json(__('ui.show_less'));
                }
                if (icon) {
                    icon.className = isHidden ? 'fas fa-chevron-down text-xs' : 'fas fa-chevron-up text-xs';
                }
            });
        }

        function createBubble() {
            const container = document.getElementById('bubble-container');
            if (!container) return;

            const bubble = document.createElement('div');
            bubble.classList.add('bubble');

            const size = Math.random() * 42 + 8;
            bubble.style.width = size + 'px';
            bubble.style.height = size + 'px';
            bubble.style.left = Math.random() * 100 + '%';
            bubble.style.bottom = '-24px';
            bubble.style.animationDuration = Math.random() * 5 + 4 + 's';
            bubble.style.animationDelay = Math.random() * 2 + 's';

            container.appendChild(bubble);
            setTimeout(() => bubble.remove(), 10000);
        }

        setInterval(createBubble, 420);
        for (let i = 0; i < 10; i++) {
            setTimeout(createBubble, i * 180);
        }
    </script>
</body>
</html>
