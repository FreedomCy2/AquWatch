<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ !empty($isHistory) ? __('ui.past_notification_history') : __('ui.recent_alerts') }} - AquWatch</title>
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
            height: 90px;
            animation: waveShift 9s ease-in-out infinite alternate;
        }

        @keyframes waveShift {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-18px) translateY(4px); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-900">
    <div class="wave-bg">
        <svg class="wave-svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#4fc3f7" fill-opacity="0.65" d="M0,256L60,250.7C120,245,240,235,360,229.3C480,224,600,224,720,229.3C840,235,960,245,1080,245.3C1200,245,1320,235,1380,229.3L1440,224L1440,320L0,320Z"></path>
        </svg>
    </div>

    <main class="relative z-10 px-4 py-6 md:px-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-xl backdrop-blur-md md:p-7">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 class="text-3xl font-extrabold text-cyan-950 md:text-4xl">{{ !empty($isHistory) ? __('ui.past_notification_history') : __('ui.recent_alerts') }}</h1>
                        <p class="mt-1 text-cyan-900/80">
                            {{ !empty($isHistory)
                                ? 'Extended timeline of past flood, rain, and connectivity events.'
                                : 'Only the latest key alerts are shown here to avoid spam during long events.' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-white transition hover:bg-cyan-800">
                            {{ __('ui.dashboard') }}
                        </a>
                        @if (!empty($isHistory))
                            <a href="{{ route('contents.notifications') }}" class="rounded-xl bg-white/80 px-4 py-2 text-cyan-900 transition hover:bg-white">
                                {{ __('ui.recent_alerts') }}
                            </a>
                        @else
                            <a href="{{ route('contents.notifications.history') }}" class="rounded-xl bg-white/80 px-4 py-2 text-cyan-900 transition hover:bg-white">
                                {{ __('ui.past_history') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-red-200 bg-red-50/80 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-red-800">{{ __('ui.critical') }}</p>
                    <p class="mt-1 text-3xl font-bold text-red-900">{{ number_format($summary['critical']) }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-amber-800">{{ __('ui.warning') }}</p>
                    <p class="mt-1 text-3xl font-bold text-amber-900">{{ number_format($summary['warning']) }}</p>
                </div>
                <div class="rounded-2xl border border-sky-200 bg-sky-50/80 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-sky-800">{{ __('ui.info') }}</p>
                    <p class="mt-1 text-3xl font-bold text-sky-900">{{ number_format($summary['info']) }}</p>
                </div>
            </div>

            <div class="rounded-3xl border border-white/60 bg-white/70 p-5 shadow-xl backdrop-blur-md md:p-6">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-bell text-amber-500"></i>
                    <h2 class="text-xl font-bold text-cyan-950">{{ !empty($isHistory) ? __('ui.timeline_history') : __('ui.timeline_recent') }}</h2>
                </div>

                @if($alerts->isEmpty())
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 text-emerald-900">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ __('ui.no_active_alerts') }}
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($alerts as $alert)
                            @php
                                $severity = $alert['severity'] ?? 'info';
                                $title = $alert['title'] ?? 'Alert';
                                $message = $alert['message'] ?? '';
                                $source = strtoupper((string) ($alert['source'] ?? 'sensor'));
                                $occurredAt = $alert['occurred_at'];

                                $isCritical = $severity === 'critical';
                                $isWarning = $severity === 'warning';

                                $cardClass = $isCritical
                                    ? 'border-red-200 bg-red-50/80'
                                    : ($isWarning ? 'border-amber-200 bg-amber-50/80' : 'border-sky-200 bg-sky-50/80');

                                $badgeClass = $isCritical
                                    ? 'bg-red-100 text-red-800'
                                    : ($isWarning ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800');

                                $iconClass = $isCritical
                                    ? 'fa-triangle-exclamation text-red-600'
                                    : ($isWarning ? 'fa-bell text-amber-600' : 'fa-circle-info text-sky-600');
                            @endphp

                            <article class="rounded-2xl border p-4 {{ $cardClass }}">
                                <div class="flex flex-wrap items-start gap-3">
                                    <i class="fas {{ $iconClass }} mt-1 text-lg"></i>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-semibold text-slate-900">{{ $title }}</h3>
                                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                                                {{ strtoupper($severity) }}
                                            </span>
                                            <span class="rounded-full bg-white/70 px-2 py-0.5 text-xs font-medium text-slate-700">
                                                {{ $source }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-800">{{ $message }}</p>
                                    </div>

                                    <time class="text-xs text-slate-600">
                                        {{ $occurredAt ? $occurredAt->diffForHumans() : '-' }}
                                    </time>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
