@php
    use App\Models\FlowReading;
    use App\Models\FloodReading;
    use App\Models\RainReading;

    $dashboardLatestFlowPerSensor = FlowReading::query()
        ->select(['sensor_id', 'flow_lpm', 'total_ml', 'created_at'])
        ->orderByDesc('created_at')
        ->get()
        ->unique('sensor_id')
        ->take(2)
        ->values();

    $dashboardFlowLpm = round((float) $dashboardLatestFlowPerSensor->sum(function ($reading): float {
        return (float) $reading->flow_lpm;
    }), 3);
    $dashboardFlowBarPercent = min(100, max(0, ($dashboardFlowLpm / 20) * 100));

    $todayStart = now()->startOfDay();
    $todayFirst = FlowReading::query()
        ->where('created_at', '>=', $todayStart)
        ->orderBy('created_at')
        ->first();
    $todayLast = FlowReading::query()
        ->where('created_at', '>=', $todayStart)
        ->orderByDesc('created_at')
        ->first();

    $dashboardDailyMl = max(0, (int) ($todayLast?->total_ml ?? 0) - (int) ($todayFirst?->total_ml ?? 0));
    $dashboardDailyL = $dashboardDailyMl / 1000;

    $dashboardActiveFlowSensors = $dashboardLatestFlowPerSensor
        ->filter(function ($reading): bool {
            return $reading->created_at?->greaterThanOrEqualTo(now()->subSeconds(15)) ?? false;
        })
        ->count();

    $dashboardHasRecentFlow = $dashboardActiveFlowSensors > 0;

    $dashboardLatestRain = RainReading::query()
        ->orderByDesc('created_at')
        ->first();

    $dashboardRainAnalog = (int) ($dashboardLatestRain?->analog_value ?? 0);
    $dashboardRainLevel = (string) ($dashboardLatestRain?->intensity_level ?? 'no_rain');
    $dashboardRainLabel = match ($dashboardRainLevel) {
        'heavy_rain' => 'Heavy Rain',
        'rain' => 'Rain',
        default => 'No Rain',
    };
    $dashboardHasRecentRain = $dashboardLatestRain?->created_at?->greaterThanOrEqualTo(now()->subSeconds(20)) ?? false;
    $dashboardRainBarPercent = match ($dashboardRainLevel) {
        'heavy_rain' => 92,
        'rain' => 58,
        default => 18,
    };

    $dashboardLatestFlood = FloodReading::query()
        ->orderByDesc('created_at')
        ->first();

    $dashboardFloodStatus = (string) ($dashboardLatestFlood?->status ?? 'SAFE / DRY');
    $dashboardFloodRiseSec = (int) ($dashboardLatestFlood?->rise_time_sec ?? 0);
    $dashboardHasRecentFlood = $dashboardLatestFlood?->created_at?->greaterThanOrEqualTo(now()->subSeconds(20)) ?? false;
    $dashboardFloodCardState = match ($dashboardFloodStatus) {
        'CRITICAL' => 'Critical',
        'FLASH FLOOD WARNING' => 'Flash Flood Warning',
        'NORMAL RISE' => 'Normal Rise',
        'LEVEL 1 DETECTED' => 'Level 1 Detected',
        default => 'Safe / Dry',
    };
    $dashboardFloodBarPercent = match ($dashboardFloodStatus) {
        'CRITICAL' => 100,
        'FLASH FLOOD WARNING' => 82,
        'NORMAL RISE' => 62,
        'LEVEL 1 DETECTED' => 38,
        default => 12,
    };
    $dashboardActiveSensors = $dashboardActiveFlowSensors + ((int) $dashboardHasRecentRain) + ((int) $dashboardHasRecentFlood);

    $dashboardRecentAlerts = collect();

    if ($dashboardHasRecentFlood && $dashboardLatestFlood) {
        $dashboardRecentAlerts->push([
            'icon' => 'fa-water text-blue-500',
            'text' => 'Flood sensor: '.$dashboardFloodCardState.' (rise '.number_format($dashboardFloodRiseSec).'s)',
            'time' => $dashboardLatestFlood->created_at,
        ]);
    }

    if ($dashboardHasRecentRain && $dashboardLatestRain) {
        $dashboardRecentAlerts->push([
            'icon' => 'fa-cloud-rain text-blue-500',
            'text' => 'Rain sensor: '.$dashboardRainLabel.' (value '.number_format($dashboardRainAnalog).')',
            'time' => $dashboardLatestRain->created_at,
        ]);
    }

    if ($dashboardLatestFlowPerSensor->isNotEmpty()) {
        $dashboardRecentAlerts->push([
            'icon' => 'fa-tint text-cyan-500',
            'text' => 'Flow rate: '.number_format($dashboardFlowLpm, 3).' L/min (combined)',
            'time' => $dashboardLatestFlowPerSensor->max('created_at'),
        ]);
    }

    $dashboardRecentAlerts = $dashboardRecentAlerts
        ->sortByDesc('time')
        ->take(4)
        ->values();

    $alertsSeenAtRaw = session('alerts_seen_at');
    $alertsSeenAt = is_string($alertsSeenAtRaw) && $alertsSeenAtRaw !== ''
        ? \Illuminate\Support\Carbon::parse($alertsSeenAtRaw)
        : null;

    $dashboardRecentAlertCount = $dashboardRecentAlerts
        ->filter(function (array $alert) use ($alertsSeenAt): bool {
            $time = $alert['time'] ?? null;

            if (! $time) {
                return false;
            }

            return ! $alertsSeenAt || $time->greaterThan($alertsSeenAt);
        })
        ->count();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AquWatch | Ocean Intelligence</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Chart.js for interactive graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

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

        @keyframes gentleWave {
            0% { transform: translateX(0px) translateY(0px); }
            100% { transform: translateX(-15px) translateY(3px); }
        }

        .bubble {
            position: fixed;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            box-shadow: 0 0 12px rgba(255,255,240,0.6);
            pointer-events: none;
            z-index: 2;
            animation: floatUp linear infinite;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0) scale(0.3);
                opacity: 0.7;
            }
            100% {
                transform: translateY(-100vh) scale(1.2);
                opacity: 0;
            }
        }

        .dashboard-card {
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            backdrop-filter: blur(8px);
        }

        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 40px -12px rgba(0, 100, 120, 0.4);
        }

        @keyframes softPulse {
            0%, 100% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.02); }
        }

        .stat-value {
            animation: softPulse 3s ease-in-out infinite;
        }

        .ripple-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .ripple-btn:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(255,255,255,0.4) 10%, transparent 10%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10);
            opacity: 0;
            transition: transform 0.5s, opacity 0.8s;
        }

        .ripple-btn:active:after {
            transform: scale(0);
            opacity: 0.4;
            transition: 0s;
        }

        @keyframes softGlow {
            0%, 100% { filter: drop-shadow(0 4px 8px rgba(0,150,180,0.3)); }
            50% { filter: drop-shadow(0 8px 20px rgba(0,180,200,0.6)); }
        }

        .logo-glow {
            animation: softGlow 3s ease-in-out infinite;
        }

        @keyframes floatSoft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .float-icon {
            animation: floatSoft 3s ease-in-out infinite;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #b9e6f5;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #2c7da0;
            border-radius: 10px;
        }

        .chart-container {
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            transform: scale(1.01);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">    <!-- Bubble Background -->
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <!-- Wave Background -->
    <div class="wave-bg">
        <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!-- Header -->
<header class="relative z-20 flex flex-col md:flex-row justify-between md:items-center w-full max-w-7xl mx-auto px-4 md:px-6 py-4 md:py-5 gap-3">        <div class="flex items-center gap-2 md:gap-3 group cursor-pointer transition-all duration-300">
<img src="{{ asset('images/logo.png') }}" 
     alt="AquWatch Logo"
     class="h-10 w-auto drop-shadow-md">
            <h1 class="text-xl md:text-2xl font-black bg-gradient-to-r from-blue-800 to-teal-700 bg-clip-text text-transparent">AquWatch</h1>
            <span class="ml-1 md:ml-2 text-xs bg-white/40 backdrop-blur-sm px-2 md:px-3 py-1 rounded-full text-blue-700">
                <i class="fas fa-chart-line text-xs"></i> LIVE
            </span>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 md:gap-4 w-full md:w-auto">
            <div class="hidden md:flex items-center gap-2 bg-white/40 backdrop-blur-sm px-4 py-2 rounded-full text-blue-800 text-sm">
                <i class="fas fa-clock"></i>
                <span id="live-time">--:-- --</span>
            </div>

            <a href="{{ route('contents.notifications') }}"
               class="relative flex items-center gap-2 bg-white/80 hover:bg-white px-3 md:px-4 py-2 rounded-xl shadow border border-white/70 text-blue-800 font-semibold transition"
               title="Notifications">
                <i class="fas fa-bell text-amber-600"></i>
                <span class="hidden sm:inline">Notifications</span>
                @if ($dashboardRecentAlertCount > 0)
                    <span class="absolute -top-2 -right-2 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                        {{ $dashboardRecentAlertCount }}
                    </span>
                @endif
            </a>

            @if (Auth::user()->isPro())
                <a href="{{ route('contents.ai-chat') }}"
                   class="flex items-center gap-2 bg-white/80 hover:bg-white px-3 md:px-4 py-2 rounded-xl shadow border border-white/70 text-blue-800 font-semibold transition">
                    <i class="fas fa-comments text-cyan-600"></i>
                    <span class="hidden sm:inline">AI Chat</span>
                </a>
                <a href="{{ route('contents.ai-insights') }}"
                   class="flex items-center gap-2 bg-white/80 hover:bg-white px-3 md:px-4 py-2 rounded-xl shadow border border-white/70 text-blue-800 font-semibold transition">
                    <i class="fas fa-robot text-cyan-600"></i>
                    <span class="hidden sm:inline">AI Insights</span>
                </a>
            @else
                <a href="{{ route('plans') }}"
                   class="flex items-center gap-2 bg-white/80 hover:bg-white px-3 md:px-4 py-2 rounded-xl shadow border border-white/70 text-blue-800 font-semibold transition">
                    <i class="fas fa-lock text-amber-600"></i>
                    <span class="hidden sm:inline">Unlock AI</span>
                </a>
            @endif

            <a href="{{ route('plans') }}"
               class="flex items-center gap-2 bg-white/80 hover:bg-white px-3 md:px-4 py-2 rounded-xl shadow border border-white/70 text-blue-800 font-semibold transition">
                <i class="fas fa-crown text-amber-500"></i>
                <span class="hidden sm:inline">{{ Auth::user()->isPro() ? 'Pro Active' : 'Upgrade' }}</span>
            </a>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileButton"
                        type="button"
                        class="flex items-center gap-3 bg-white/80 hover:bg-white px-3 py-2 rounded-2xl shadow border border-white/70 transition">
<div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-300 to-slate-500 flex items-center justify-center text-white font-bold overflow-hidden">
    @if(Auth::user()->profile && Auth::user()->profile->photo)
        <img src="{{ asset('storage/' . Auth::user()->profile->photo) }}"
             alt="Profile Photo"
             class="w-full h-full object-cover">
    @else
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    @endif
</div>

                    <div class="hidden sm:block text-left">
                        <div class="text-sm font-semibold text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500">My Account</div>
                    </div>

                    <i class="fas fa-chevron-down text-slate-600 text-xs"></i>
                </button>

                <div id="profileMenu"
                     class="hidden absolute right-0 top-full mt-3 w-56 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-slate-500 break-all">{{ Auth::user()->email }}</p>
                    </div>

                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-3 px-4 py-3 text-slate-700 hover:bg-sky-50 transition">
                        <i class="far fa-user text-slate-600"></i>
                        <span>Profile</span>
                    </a>

                          <a href="{{ route('plans') }}"
                              class="flex items-center gap-3 px-4 py-3 text-slate-700 hover:bg-sky-50 transition">
                                <i class="fas fa-crown text-amber-500"></i>
                                <span>Upgrade Plan</span>
                          </a>

                    <a href="{{ route('account.settings.edit') }}"
                       class="flex items-center gap-3 px-4 py-3 text-slate-700 hover:bg-sky-50 transition">
                        <i class="fas fa-gear text-slate-600"></i>
                        <span>Settings</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
<main class="relative z-10 w-full max-w-7xl mx-auto px-4 md:px-6 py-6 pb-20">
        <!-- Welcome Banner -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600/20 to-cyan-600/20 backdrop-blur-sm rounded-2xl p-6 border border-white/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-2">
                            Welcome back,
                            <span class="bg-gradient-to-r from-cyan-700 to-blue-800 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}
                            </span>
                        </h2>
                        <p class="text-blue-800/80">Real-time water intelligence at your fingertips</p>
                    </div>

                    <div class="flex gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-teal-700 stat-value" id="active-sensors">{{ $dashboardActiveSensors }}</div>
                            <div class="text-xs text-blue-700">Active Sensors</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-tint text-cyan-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Current Flow</div>
                <div class="text-xl font-bold text-blue-900" id="flow-rate">{{ number_format($dashboardFlowLpm, 3) }} L/min</div>
            </div>
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-cloud-rain text-blue-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Rain Status</div>
                <div class="text-xl font-bold text-blue-900" id="rainfall">{{ $dashboardRainLabel }}</div>
            </div>
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-water text-teal-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Flood Level</div>
                <div class="text-xl font-bold text-blue-900" id="flood-level">{{ $dashboardFloodCardState }}</div>
            </div>
        </div>

        <!-- Main Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Rain Display -->
            <a href="contents/rain-display" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">🌧️</div>
                    <div id="rain-state-badge" class="{{ $dashboardHasRecentRain ? 'bg-cyan-100/80 text-cyan-700' : 'bg-slate-100/80 text-slate-700' }} rounded-full px-3 py-1 text-xs">
                        <i class="fas fa-chart-simple"></i> {{ $dashboardHasRecentRain ? 'Live' : 'No recent data' }}
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Rain Display</h3>
                <p class="text-blue-700 mb-3">Real-time rainfall intensity and forecast data</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-cloud-rain"></i> <span id="rain-trend">{{ $dashboardRainLabel }}</span></span>
                    <span class="text-blue-600"><i class="fas fa-sliders"></i> Value: <span id="rain-forecast">{{ number_format($dashboardRainAnalog) }}</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" style="width: {{ $dashboardRainBarPercent }}%" id="rain-bar"></div>
                </div>
            </a>

            <!-- Flood Display -->
            <a href="contents/flood-display" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">🌊</div>
                    <div id="flood-state-badge" class="{{ $dashboardHasRecentFlood ? 'bg-blue-100/80 text-blue-700' : 'bg-slate-100/80 text-slate-700' }} rounded-full px-3 py-1 text-xs">
                        <i class="fas fa-chart-line"></i> {{ $dashboardHasRecentFlood ? 'Live' : 'No recent data' }}
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Flood Display</h3>
                <p class="text-blue-700 mb-3">Water level monitoring and flood warnings</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-arrow-trend-up"></i> <span id="flood-trend">{{ $dashboardFloodCardState }}</span></span>
                    <span class="text-blue-600"><i class="fas fa-stopwatch"></i> Rise: <span id="flood-threshold">{{ number_format($dashboardFloodRiseSec) }}s</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-teal-500 to-blue-500 h-2 rounded-full" style="width: {{ $dashboardFloodBarPercent }}%" id="flood-bar"></div>
                </div>
            </a>

            <!-- Flow Display -->
            <a href="contents/flow-display" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">💧</div>
                    <div id="flow-state-badge" class="bg-emerald-100/80 rounded-full px-3 py-1 text-xs text-emerald-700">
                        <i class="fas fa-tachometer-alt"></i> {{ $dashboardHasRecentFlow ? 'Live' : 'No recent data' }}
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Flow Display</h3>
                <p class="text-blue-700 mb-3">Water flow rate and volume tracking</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-water"></i> Rate: <span id="flow-rate-detail">{{ number_format($dashboardFlowLpm, 3) }} L/min</span></span>
                    <span class="text-blue-600"><i class="fas fa-chart-line"></i> Daily: <span id="daily-volume">{{ number_format($dashboardDailyL, 2) }} L</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-cyan-500 to-emerald-500 h-2 rounded-full" style="width: {{ $dashboardFlowBarPercent }}%" id="flow-bar"></div>
                </div>
            </a>

            <!-- Graph Display -->
            <a href="contents/graph-display" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">📈</div>
                    <div class="bg-purple-100/80 rounded-full px-3 py-1 text-xs text-purple-700">
                        24h Trend
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Graph Display</h3>
                <p class="text-blue-700 mb-3">Real-time flow, rain, and flood visualization</p>
                <div class="chart-container">
                    <canvas id="waterChart" width="400" height="200" style="max-height: 180px; width: 100%"></canvas>
                </div>
            </a>

        </div>

    </main>

    <!-- Footer -->
    <footer class="relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 mt-8 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-github"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch — Protecting our waters with real-time intelligence
        </p>
    </footer>

    <script>
        // ========== BUBBLE ANIMATION ==========
        function createBubble() {
            const container = document.getElementById('bubble-container');
            if (!container) return;

            const bubble = document.createElement('div');
            bubble.classList.add('bubble');

            const size = Math.random() * 45 + 8;
            bubble.style.width = size + 'px';
            bubble.style.height = size + 'px';
            bubble.style.left = Math.random() * 100 + '%';
            bubble.style.bottom = '-20px';
            bubble.style.animationDuration = Math.random() * 5 + 4 + 's';
            bubble.style.animationDelay = Math.random() * 3 + 's';
            bubble.style.background = `rgba(255, 255, 245, ${Math.random() * 0.5 + 0.2})`;

            container.appendChild(bubble);
            setTimeout(() => bubble.remove(), 10000);
        }

        setInterval(createBubble, 400);
        for (let i = 0; i < 12; i++) {
            setTimeout(createBubble, i * 200);
        }

        // ========== LIVE TIME ==========
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const timeElement = document.getElementById('live-time');
            if (timeElement) {
                timeElement.textContent = timeStr;
            }
        }

        updateTime();
        setInterval(updateTime, 1000);

        // ========== PROFILE DROPDOWN ==========
        const profileButton = document.getElementById('profileButton');
        const profileMenu = document.getElementById('profileMenu');

        if (profileButton && profileMenu) {
            profileButton.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        }

        // ========== SIMULATE REAL-TIME DATA ==========
        const flowDataUrl = @json(route('contents.flow-display.data'));
        const rainDataUrl = @json(route('contents.rain-display.data'));
        const floodDataUrl = @json(route('contents.flood-display.data'));

        let flowRate = Number(@json($dashboardFlowLpm));
        let rainStatusText = @json($dashboardRainLabel);
        let rainAnalog = Number(@json($dashboardRainAnalog));
        let floodStatusText = @json($dashboardFloodCardState);
        let floodRiseSec = Number(@json($dashboardFloodRiseSec));
        let flowActiveSensors = Number(@json($dashboardActiveFlowSensors));
        let rainSensorIsRecent = Boolean(@json($dashboardHasRecentRain));
        let floodSensorIsRecent = Boolean(@json($dashboardHasRecentFlood));
        let dailyVolumeL = Number(@json(round($dashboardDailyL, 2)));

        function refreshActiveSensors() {
            const activeSensorsEl = document.getElementById('active-sensors');
            if (!activeSensorsEl) {
                return;
            }

            const activeCount = flowActiveSensors
                + (rainSensorIsRecent ? 1 : 0)
                + (floodSensorIsRecent ? 1 : 0);

            activeSensorsEl.textContent = String(activeCount);
        }

        const chartCanvas = document.getElementById('waterChart');
        let chart = null;

        if (chartCanvas) {
            const ctx = chartCanvas.getContext('2d');
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['6h', '5h', '4h', '3h', '2h', '1h', 'Now'],
                    datasets: [
                        {
                            label: 'Flow Rate (L/min)',
                            data: [flowRate, flowRate, flowRate, flowRate, flowRate, flowRate, flowRate],
                            borderColor: '#0e7c9e',
                            backgroundColor: 'rgba(14, 124, 158, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 10 },
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: {
                                display: true,
                                text: 'Value'
                            }
                        }
                    }
                }
            });
        }

        async function refreshFlowSummary() {
            try {
                const response = await fetch(flowDataUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json();
                const sensors = Array.isArray(payload?.sensors) ? payload.sensors : [];
                const combined = payload?.combined ?? null;
                flowRate = Number(combined?.flow_lpm ?? 0);

                const flowRateEl = document.getElementById('flow-rate');
                const flowRateDetailEl = document.getElementById('flow-rate-detail');
                const dailyVolumeEl = document.getElementById('daily-volume');
                const flowBarEl = document.getElementById('flow-bar');
                const flowStateBadgeEl = document.getElementById('flow-state-badge');

                if (flowRateEl) flowRateEl.textContent = flowRate.toFixed(3) + ' L/min';
                if (flowRateDetailEl) flowRateDetailEl.textContent = flowRate.toFixed(3) + ' L/min';

                dailyVolumeL = Number((combined?.total_ml ?? 0) / 1000);
                if (dailyVolumeEl) dailyVolumeEl.textContent = dailyVolumeL.toFixed(2) + ' L';

                const flowPercent = Math.min(100, (flowRate / 20) * 100);
                if (flowBarEl) flowBarEl.style.width = flowPercent + '%';

                if (flowStateBadgeEl) {
                    flowActiveSensors = sensors.filter(sensor => Boolean(sensor?.is_recent)).length;

                    if (flowActiveSensors > 0) {
                        flowStateBadgeEl.className = 'bg-emerald-100/80 rounded-full px-3 py-1 text-xs text-emerald-700';
                        flowStateBadgeEl.innerHTML = `<i class="fas fa-tachometer-alt"></i> Live (${flowActiveSensors}/${sensors.length || 2})`;
                    } else {
                        flowStateBadgeEl.className = 'bg-slate-100/80 rounded-full px-3 py-1 text-xs text-slate-700';
                        flowStateBadgeEl.innerHTML = '<i class="fas fa-tachometer-alt"></i> No recent data';
                    }
                }

                refreshActiveSensors();
            } catch {
                // Keep last known values on temporary request failures.
            }
        }

        function getRainBarPercent(level) {
            if (level === 'heavy_rain') return 92;
            if (level === 'rain') return 58;
            return 18;
        }

        function getRainLabel(level) {
            if (level === 'heavy_rain') return 'Heavy Rain';
            if (level === 'rain') return 'Rain';
            return 'No Rain';
        }

        async function refreshRainSummary() {
            try {
                const response = await fetch(rainDataUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json();
                const latest = payload?.latest;
                const level = String(latest?.intensity_level ?? 'no_rain');
                rainStatusText = getRainLabel(level);
                rainAnalog = Number(latest?.analog_value ?? 0);

                const rainfallEl = document.getElementById('rainfall');
                const rainTrendEl = document.getElementById('rain-trend');
                const rainForecastEl = document.getElementById('rain-forecast');
                const rainBarEl = document.getElementById('rain-bar');
                const rainStateBadgeEl = document.getElementById('rain-state-badge');

                if (rainfallEl) rainfallEl.textContent = rainStatusText;
                if (rainTrendEl) rainTrendEl.textContent = rainStatusText;
                if (rainForecastEl) rainForecastEl.textContent = rainAnalog.toLocaleString();
                if (rainBarEl) rainBarEl.style.width = getRainBarPercent(level) + '%';

                if (rainStateBadgeEl) {
                    if (latest?.is_recent) {
                        rainSensorIsRecent = true;
                        rainStateBadgeEl.className = 'bg-cyan-100/80 rounded-full px-3 py-1 text-xs text-cyan-700';
                        rainStateBadgeEl.innerHTML = '<i class="fas fa-chart-simple"></i> Live';
                    } else {
                        rainSensorIsRecent = false;
                        rainStateBadgeEl.className = 'bg-slate-100/80 rounded-full px-3 py-1 text-xs text-slate-700';
                        rainStateBadgeEl.innerHTML = '<i class="fas fa-chart-simple"></i> No recent data';
                    }
                }

                refreshActiveSensors();
            } catch {
                // Keep last known values on temporary request failures.
            }
        }

        function floodStatusLabel(status) {
            if (status === 'CRITICAL') return 'Critical';
            if (status === 'FLASH FLOOD WARNING') return 'Flash Flood Warning';
            if (status === 'NORMAL RISE') return 'Normal Rise';
            if (status === 'LEVEL 1 DETECTED') return 'Level 1 Detected';
            return 'Safe / Dry';
        }

        function floodBarPercent(status) {
            if (status === 'CRITICAL') return 100;
            if (status === 'FLASH FLOOD WARNING') return 82;
            if (status === 'NORMAL RISE') return 62;
            if (status === 'LEVEL 1 DETECTED') return 38;
            return 12;
        }

        async function refreshFloodSummary() {
            try {
                const response = await fetch(floodDataUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json();
                const latest = payload?.latest;
                const status = String(latest?.status ?? 'SAFE / DRY');

                floodStatusText = floodStatusLabel(status);
                floodRiseSec = Number(latest?.rise_time_sec ?? 0);

                const floodLevelEl = document.getElementById('flood-level');
                const floodTrendEl = document.getElementById('flood-trend');
                const floodThresholdEl = document.getElementById('flood-threshold');
                const floodBarEl = document.getElementById('flood-bar');
                const floodStateBadgeEl = document.getElementById('flood-state-badge');

                if (floodLevelEl) floodLevelEl.textContent = floodStatusText;
                if (floodTrendEl) floodTrendEl.textContent = floodStatusText;
                if (floodThresholdEl) floodThresholdEl.textContent = floodRiseSec.toLocaleString() + 's';
                if (floodBarEl) floodBarEl.style.width = floodBarPercent(status) + '%';

                if (floodStateBadgeEl) {
                    if (latest?.is_recent) {
                        floodSensorIsRecent = true;
                        floodStateBadgeEl.className = 'bg-blue-100/80 rounded-full px-3 py-1 text-xs text-blue-700';
                        floodStateBadgeEl.innerHTML = '<i class="fas fa-chart-line"></i> Live';
                    } else {
                        floodSensorIsRecent = false;
                        floodStateBadgeEl.className = 'bg-slate-100/80 rounded-full px-3 py-1 text-xs text-slate-700';
                        floodStateBadgeEl.innerHTML = '<i class="fas fa-chart-line"></i> No recent data';
                    }
                }

                refreshActiveSensors();
            } catch {
                // Keep last known values on temporary request failures.
            }
        }

        function updateChartSeries() {
            if (chart) {
                const newFlowData = [...chart.data.datasets[0].data.slice(1), Math.round(flowRate)];
                chart.data.datasets[0].data = newFlowData;
                chart.update('none');
            }
        }

        refreshFlowSummary();
        refreshRainSummary();
        refreshFloodSummary();
        refreshActiveSensors();
        setInterval(refreshFlowSummary, 5000);
        setInterval(refreshRainSummary, 5000);
        setInterval(refreshFloodSummary, 5000);
        setInterval(updateChartSeries, 4000);

        // ========== RIPPLE EFFECT ==========
        document.querySelectorAll('.ripple-btn, .dashboard-card, button').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const rippleDiv = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                rippleDiv.style.width = rippleDiv.style.height = size + 'px';
                rippleDiv.style.position = 'absolute';
                rippleDiv.style.top = y + 'px';
                rippleDiv.style.left = x + 'px';
                rippleDiv.style.background = 'radial-gradient(circle, rgba(255,255,255,0.6) 0%, rgba(255,255,255,0) 80%)';
                rippleDiv.style.borderRadius = '50%';
                rippleDiv.style.pointerEvents = 'none';
                rippleDiv.style.transform = 'scale(0)';
                rippleDiv.style.transition = 'transform 0.5s ease-out, opacity 0.6s';
                rippleDiv.style.opacity = '1';

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(rippleDiv);

                requestAnimationFrame(() => {
                    rippleDiv.style.transform = 'scale(4)';
                    rippleDiv.style.opacity = '0';
                });

                setTimeout(() => rippleDiv.remove(), 800);
            });
        });


        console.log('🌊 AquWatch Dashboard — Real-time monitoring active');
    </script>
</body>
</html>