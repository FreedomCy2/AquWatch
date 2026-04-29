<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>AquWatch Plans</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .plan-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 40px -16px rgba(0, 94, 120, 0.35);
        }

        .badge-pulse {
            animation: badgePulse 2.4s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); opacity: 0.95; }
            50% { transform: scale(1.04); opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-gradient-to-br from-sky-100 via-cyan-100 to-teal-100 text-slate-900">
    <header class="max-w-6xl mx-auto px-4 py-6 sm:px-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 self-start min-w-0">
            <img src="{{ asset('images/logo.png') }}" alt="AquWatch Logo" class="h-10 w-auto">
            <span class="text-xl sm:text-2xl font-extrabold bg-gradient-to-r from-blue-800 to-teal-700 bg-clip-text text-transparent truncate">AquWatch</span>
        </a>

        <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center sm:gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-center px-3 sm:px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white hover:from-blue-700 hover:to-cyan-700 transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-center px-3 sm:px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white hover:from-blue-700 hover:to-cyan-700 transition">Login</a>
            @endauth
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 pb-14">
        <section class="text-center mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs bg-white/70 border border-cyan-200 text-cyan-800 badge-pulse">
                <i class="fas fa-droplet"></i>
                Flood Monitoring Plans
            </span>
            <h1 class="mt-4 text-4xl md:text-5xl font-black text-blue-900">Choose the plan that fits your operations</h1>
            <p class="mt-3 text-blue-900/80 max-w-2xl mx-auto">Core flood safety stays simple, while advanced Pro analytics and AI insights can be unlocked as you scale.</p>
        </section>

        @if (session('success'))
            <section class="mb-6 rounded-xl border border-emerald-300 bg-emerald-100/80 px-4 py-3 text-emerald-900">
                {{ session('success') }}
            </section>
        @endif

        @if (session('error'))
            <section class="mb-6 rounded-xl border border-rose-300 bg-rose-100/80 px-4 py-3 text-rose-900">
                {{ session('error') }}
            </section>
        @endif

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <article class="plan-card bg-white/75 backdrop-blur-sm rounded-2xl border border-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-blue-900">Free</h2>
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">Starter</span>
                </div>
                <p class="mt-1 text-sm text-blue-900/70">For single-site testing and small deployments.</p>
                <p class="mt-5 text-3xl font-extrabold text-blue-900">$0<span class="text-sm font-semibold text-blue-900/60">/month</span></p>
                <ul class="mt-5 space-y-2 text-sm text-blue-900/85">
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Live flow, rain, flood status</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>In-app alerts</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>7-day history</li>
                </ul>
                @auth
                    @if ($currentPlan === 'free')
                        <button type="button" class="mt-6 w-full py-2.5 rounded-xl bg-white border border-cyan-200 text-cyan-800 font-semibold cursor-default">
                            Current Plan
                        </button>
                    @else
                        <form method="POST" action="{{ route('plans.switch') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="plan_tier" value="free">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-white border border-cyan-300 text-cyan-800 font-semibold hover:bg-cyan-50 transition">
                                Switch to Free
                            </button>
                        </form>
                    @endif
                @else
                    <button type="button" class="mt-6 w-full py-2.5 rounded-xl bg-white border border-cyan-200 text-cyan-800 font-semibold cursor-default">
                        Available
                    </button>
                @endauth
            </article>

            <article class="plan-card bg-white/85 backdrop-blur-sm rounded-2xl border-2 border-cyan-400 p-6 shadow-xl relative">
                <div class="absolute -top-3 right-4 px-3 py-1 text-xs rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold">Most Popular</div>
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-blue-900">Pro</h2>
                    <span class="text-xs px-2 py-1 rounded-full bg-cyan-100 text-cyan-700">Recommended</span>
                </div>
                <p class="mt-1 text-sm text-blue-900/70">For power users who need deeper insights.</p>
                <p class="mt-5 text-3xl font-extrabold text-blue-900">
                    $7.99<span class="text-sm font-semibold text-blue-900/60"> first month</span>
                    <span class="ml-2 text-base font-semibold line-through text-blue-900/55">$12.99</span>
                </p>
                <p class="mt-1 text-sm text-blue-900/75">Renews at $12.99/month after first month.</p>
                <ul class="mt-5 space-y-2 text-sm text-blue-900/85">
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Everything in Free</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>AI assistant insights</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>90-day history and exports</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>SMS escalation alerts</li>
                </ul>
                @auth
                    @if ($currentPlan === 'pro')
                        <a href="{{ route('contents.ai-insights') }}" class="mt-6 block w-full py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-center hover:bg-emerald-700 transition">
                            Open AI Insights
                        </a>
                    @else
                        <form method="POST" action="{{ route('plans.switch') }}" class="mt-6">
                            @csrf
                            <input type="hidden" name="plan_tier" value="pro">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold hover:from-cyan-700 hover:to-blue-700 transition">
                                Upgrade to Pro
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-6 block w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold text-center hover:from-cyan-700 hover:to-blue-700 transition">
                        Login to Upgrade
                    </a>
                @endauth
            </article>

        </section>

        <section class="mt-8 bg-white/60 border border-white rounded-2xl p-5 text-sm text-blue-900/80">
            <p><strong>Note:</strong> Pro activation is now real inside the app account state. External payment billing is still not wired.</p>
        </section>
    </main>

</body>
</html>
