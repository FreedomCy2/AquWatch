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
<body class="min-h-screen bg-gradient-to-br from-sky-100 via-cyan-100 to-teal-100 text-slate-900">
    <header class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/Logo.png') }}" alt="AquWatch Logo" class="h-10 w-auto">
            <span class="text-2xl font-extrabold bg-gradient-to-r from-blue-800 to-teal-700 bg-clip-text text-transparent">AquWatch</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="px-4 py-2 rounded-lg bg-white/70 border border-cyan-200 text-cyan-800 hover:bg-white transition">Home</a>
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white hover:from-blue-700 hover:to-cyan-700 transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-600 text-white hover:from-blue-700 hover:to-cyan-700 transition">Login</a>
            @endauth
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 pb-14">
        <section class="text-center mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs bg-white/70 border border-cyan-200 text-cyan-800 badge-pulse">
                <i class="fas fa-droplet"></i>
                Flood Monitoring Plans
            </span>
            <h1 class="mt-4 text-4xl md:text-5xl font-black text-blue-900">Choose the plan that fits your operations</h1>
            <p class="mt-3 text-blue-900/80 max-w-2xl mx-auto">Dummy pricing page for now. Core flood safety stays simple, while advanced features can be unlocked as you scale.</p>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                <button type="button" class="mt-6 w-full py-2.5 rounded-xl bg-white border border-cyan-200 text-cyan-800 font-semibold cursor-default">Current Base Plan</button>
            </article>

            <article class="plan-card bg-white/85 backdrop-blur-sm rounded-2xl border-2 border-cyan-400 p-6 shadow-xl relative">
                <div class="absolute -top-3 right-4 px-3 py-1 text-xs rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold">Most Popular</div>
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-blue-900">Pro</h2>
                    <span class="text-xs px-2 py-1 rounded-full bg-cyan-100 text-cyan-700">Recommended</span>
                </div>
                <p class="mt-1 text-sm text-blue-900/70">For power users who need deeper insights.</p>
                <p class="mt-5 text-3xl font-extrabold text-blue-900">$9<span class="text-sm font-semibold text-blue-900/60">/month</span></p>
                <ul class="mt-5 space-y-2 text-sm text-blue-900/85">
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Everything in Free</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>AI assistant insights</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>90-day history and exports</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>SMS escalation alerts</li>
                </ul>
                <button type="button" class="mt-6 w-full py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold" onclick="showDummyToast('Pro checkout is dummy for now.')">Upgrade to Pro</button>
            </article>

            <article class="plan-card bg-white/75 backdrop-blur-sm rounded-2xl border border-white p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-blue-900">Business</h2>
                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">Multi-site</span>
                </div>
                <p class="mt-1 text-sm text-blue-900/70">For teams and organizations with many locations.</p>
                <p class="mt-5 text-3xl font-extrabold text-blue-900">$39<span class="text-sm font-semibold text-blue-900/60">/month</span></p>
                <ul class="mt-5 space-y-2 text-sm text-blue-900/85">
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Everything in Pro</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Multi-site dashboard</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>Role-based access</li>
                    <li><i class="fas fa-check text-emerald-600 mr-2"></i>API access and audit logs</li>
                </ul>
                <button type="button" class="mt-6 w-full py-2.5 rounded-xl bg-white border border-cyan-200 text-cyan-800 font-semibold" onclick="showDummyToast('Business sales flow is dummy for now.')">Contact Sales</button>
            </article>
        </section>

        <section class="mt-8 bg-white/60 border border-white rounded-2xl p-5 text-sm text-blue-900/80">
            <p><strong>Note:</strong> This is a dummy plans page for UI/demo only. Subscription billing is not wired yet.</p>
        </section>
    </main>

    <div id="dummy-toast" class="hidden fixed bottom-5 right-5 bg-blue-900 text-white px-4 py-3 rounded-lg shadow-xl text-sm"></div>

    <script>
        function showDummyToast(message) {
            const toast = document.getElementById('dummy-toast');
            if (!toast) {
                return;
            }

            toast.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 1800);
        }
    </script>
</body>
</html>
