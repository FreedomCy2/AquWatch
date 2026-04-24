<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Insights - AquWatch Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-900">
    <main class="px-4 py-6 md:px-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <section class="rounded-3xl border border-white/60 bg-white/75 p-6 shadow-xl backdrop-blur-md">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="inline-flex items-center gap-2 rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800">
                            <i class="fas fa-crown text-amber-500"></i>
                            Pro Feature
                        </p>
                        <h1 class="mt-3 text-3xl font-extrabold text-cyan-950 md:text-4xl">AI Insights</h1>
                        <p class="mt-1 text-cyan-900/80">Automated analysis generated from your latest flow, rain, flood, and sensor heartbeat patterns.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-cyan-700 px-4 py-2 text-white transition hover:bg-cyan-800">Dashboard</a>
                        <a href="{{ route('contents.notifications') }}" class="rounded-xl bg-white/80 px-4 py-2 text-cyan-900 transition hover:bg-white">Notifications</a>
                    </div>
                </div>
            </section>

            @if (session('success'))
                <section class="rounded-xl border border-emerald-300 bg-emerald-100/80 px-4 py-3 text-emerald-900">
                    {{ session('success') }}
                </section>
            @endif

            <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <article class="rounded-2xl border border-white/60 bg-white/70 p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-slate-600">Risk score</p>
                    <p class="mt-1 text-4xl font-extrabold text-cyan-950">{{ number_format($insights['risk_score']) }}</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $insights['risk_label'] }} risk</p>
                </article>
                <article class="rounded-2xl border border-white/60 bg-white/70 p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-slate-600">Flood status</p>
                    <p class="mt-1 text-2xl font-bold text-cyan-950">{{ $insights['flood_status'] }}</p>
                    <p class="mt-1 text-sm text-slate-700">Warnings in 1h: {{ number_format($insights['flood_warning_count']) }}</p>
                </article>
                <article class="rounded-2xl border border-white/60 bg-white/70 p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-slate-600">Rain level</p>
                    <p class="mt-1 text-2xl font-bold text-cyan-950">{{ ucfirst($insights['rain_level']) }}</p>
                    <p class="mt-1 text-sm text-slate-700">Heavy rain events in 1h: {{ number_format($insights['heavy_rain_count']) }}</p>
                </article>
                <article class="rounded-2xl border border-white/60 bg-white/70 p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-slate-600">Online sensors</p>
                    <p class="mt-1 text-4xl font-extrabold text-cyan-950">{{ number_format($insights['online_sensors']) }}</p>
                    <p class="mt-1 text-sm text-slate-700">Generated {{ $insights['generated_at']->diffForHumans() }}</p>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article class="rounded-2xl border border-white/60 bg-white/70 p-5 shadow-lg">
                    <h2 class="text-lg font-bold text-cyan-950">Flow analysis</h2>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl bg-cyan-50 p-3">
                            <p class="text-xs uppercase text-cyan-700">Average</p>
                            <p class="text-lg font-bold text-cyan-900">{{ number_format($insights['flow_avg'], 3) }}</p>
                            <p class="text-xs text-cyan-700">L/min</p>
                        </div>
                        <div class="rounded-xl bg-cyan-50 p-3">
                            <p class="text-xs uppercase text-cyan-700">Min</p>
                            <p class="text-lg font-bold text-cyan-900">{{ number_format($insights['flow_min'], 3) }}</p>
                            <p class="text-xs text-cyan-700">L/min</p>
                        </div>
                        <div class="rounded-xl bg-cyan-50 p-3">
                            <p class="text-xs uppercase text-cyan-700">Max</p>
                            <p class="text-lg font-bold text-cyan-900">{{ number_format($insights['flow_max'], 3) }}</p>
                            <p class="text-xs text-cyan-700">L/min</p>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-white/60 bg-white/70 p-5 shadow-lg">
                    <h2 class="text-lg font-bold text-cyan-950">AI recommendations</h2>
                    <ul class="mt-3 space-y-2 text-sm text-slate-800">
                        @foreach ($recommendations as $recommendation)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-wand-magic-sparkles mt-0.5 text-cyan-600"></i>
                                <span>{{ $recommendation }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            </section>
        </div>
    </main>
</body>
</html>
