<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Dashboard - AquaWatch | Ocean Intelligence</title>

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

<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">

    <!-- Bubble Background -->
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
    <header class="relative z-20 flex justify-between items-center px-6 py-5 max-w-7xl mx-auto">
        <div class="flex items-center gap-3 group cursor-pointer transition-all duration-300">
            <i class="fas fa-water text-3xl text-cyan-700 group-hover:text-cyan-800 transition-all drop-shadow-md"></i>
            <h1 class="text-2xl font-black bg-gradient-to-r from-blue-800 to-teal-700 bg-clip-text text-transparent">AquaWatch</h1>
            <span class="ml-2 text-xs bg-white/40 backdrop-blur-sm px-3 py-1 rounded-full text-blue-700">
                <i class="fas fa-chart-line text-xs"></i> LIVE
            </span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:flex items-center gap-2 bg-white/40 backdrop-blur-sm px-4 py-2 rounded-full text-blue-800 text-sm">
                <i class="fas fa-clock"></i>
                <span id="live-time">--:-- --</span>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileButton"
                        type="button"
                        class="flex items-center gap-3 bg-white/80 hover:bg-white px-3 py-2 rounded-2xl shadow border border-white/70 transition">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-300 to-slate-500 flex items-center justify-center text-white font-bold overflow-hidden">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
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

                    <a href="#"
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
    <main class="relative z-10 max-w-7xl mx-auto px-6 py-6 pb-20">

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
                            <div class="text-2xl font-bold text-cyan-700 stat-value" id="water-quality">98.4%</div>
                            <div class="text-xs text-blue-700">Water Quality</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-teal-700 stat-value" id="active-sensors">12</div>
                            <div class="text-xs text-blue-700">Active Sensors</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-700 stat-value" id="water-saved">2.3M</div>
                            <div class="text-xs text-blue-700">Liters Saved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-tint text-cyan-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Current Flow</div>
                <div class="text-xl font-bold text-blue-900" id="flow-rate">142 L/s</div>
            </div>
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-cloud-rain text-blue-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Rainfall</div>
                <div class="text-xl font-bold text-blue-900" id="rainfall">23 mm</div>
            </div>
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-water text-teal-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">Flood Level</div>
                <div class="text-xl font-bold text-blue-900" id="flood-level">1.2 m</div>
            </div>
            <div class="bg-white/40 backdrop-blur-sm rounded-xl p-4 text-center border border-white/50 hover:bg-white/50 transition-all">
                <i class="fas fa-chart-line text-emerald-600 text-2xl mb-2"></i>
                <div class="text-sm text-blue-700">pH Level</div>
                <div class="text-xl font-bold text-blue-900" id="ph-level">7.2</div>
            </div>
        </div>

        <!-- Main Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Rain Display -->
            <a href="#" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">🌧️</div>
                    <div class="bg-cyan-100/80 rounded-full px-3 py-1 text-xs text-cyan-700">
                        <i class="fas fa-chart-simple"></i> Updated now
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Rain Display</h3>
                <p class="text-blue-700 mb-3">Real-time rainfall intensity and forecast data</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-arrow-trend-up"></i> <span id="rain-trend">+12%</span></span>
                    <span class="text-blue-600"><i class="fas fa-calendar"></i> Next 24h: <span id="rain-forecast">15-25mm</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" style="width: 65%" id="rain-bar"></div>
                </div>
            </a>

            <!-- Flood Display -->
            <a href="#" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">🌊</div>
                    <div class="bg-blue-100/80 rounded-full px-3 py-1 text-xs text-blue-700">
                        <i class="fas fa-chart-line"></i> Alert Level: Normal
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Flood Display</h3>
                <p class="text-blue-700 mb-3">Water level monitoring and flood warnings</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-arrow-trend-up"></i> <span id="flood-trend">+0.1m</span></span>
                    <span class="text-blue-600"><i class="fas fa-gauge-high"></i> Threshold: <span id="flood-threshold">2.5m</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-teal-500 to-blue-500 h-2 rounded-full" style="width: 48%" id="flood-bar"></div>
                </div>
            </a>

            <!-- Flow Display -->
            <a href="#" class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300 group block">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl group-hover:scale-110 transition-transform duration-300">💧</div>
                    <div class="bg-emerald-100/80 rounded-full px-3 py-1 text-xs text-emerald-700">
                        <i class="fas fa-tachometer-alt"></i> Optimal
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Flow Display</h3>
                <p class="text-blue-700 mb-3">Water flow rate and volume tracking</p>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-blue-600"><i class="fas fa-water"></i> Rate: <span id="flow-rate-detail">142 L/s</span></span>
                    <span class="text-blue-600"><i class="fas fa-chart-line"></i> Daily: <span id="daily-volume">12,300 m³</span></span>
                </div>
                <div class="mt-3 w-full bg-blue-200/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-cyan-500 to-emerald-500 h-2 rounded-full" style="width: 72%" id="flow-bar"></div>
                </div>
            </a>

            <!-- Graph Display -->
            <div class="dashboard-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-2xl transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div class="text-5xl float-icon">📈</div>
                    <div class="bg-purple-100/80 rounded-full px-3 py-1 text-xs text-purple-700">
                        <i class="fas fa-chart-line"></i> 24h Trend
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-blue-900 mb-2">Graph Display</h3>
                <p class="text-blue-700 mb-3">Real-time water quality & flow visualization</p>
                <div class="chart-container">
                    <canvas id="waterChart" width="400" height="200" style="max-height: 180px; width: 100%"></canvas>
                </div>
                <div class="mt-3 text-center text-xs text-blue-600">
                    <i class="fas fa-chart-simple"></i> Last 12 hours data | Auto-updates
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-5 border border-white/60">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-bell text-amber-500"></i>
                <h4 class="font-bold text-blue-900">Recent Alerts</h4>
                <span class="text-xs bg-blue-100 px-2 py-0.5 rounded-full text-blue-700">Live</span>
            </div>
            <div class="space-y-2" id="alert-container">
                <div class="flex items-center gap-3 text-sm text-blue-800">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>All systems operational</span>
                    <span class="text-xs text-blue-500 ml-auto">Just now</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-blue-800">
                    <i class="fas fa-tint text-cyan-500"></i>
                    <span>Flow rate stable at 142 L/s</span>
                    <span class="text-xs text-blue-500 ml-auto">5 min ago</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-blue-800">
                    <i class="fas fa-cloud-rain text-blue-500"></i>
                    <span>Light rainfall detected in catchment area</span>
                    <span class="text-xs text-blue-500 ml-auto">12 min ago</span>
                </div>
            </div>
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
            © {{ date('Y') }} AquaWatch — Protecting our waters with real-time intelligence
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
        let flowRate = 142;
        let rainfall = 23;
        let floodLevel = 1.2;
        let pH = 7.2;
        let waterQuality = 98.4;
        let waterSaved = 2.3;

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
                            label: 'Flow Rate (L/s)',
                            data: [138, 140, 142, 141, 143, 142, 142],
                            borderColor: '#0e7c9e',
                            backgroundColor: 'rgba(14, 124, 158, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Water Quality (%)',
                            data: [97.8, 98.0, 98.2, 98.3, 98.4, 98.4, 98.4],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
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

        function simulateDataUpdate() {
            flowRate = Math.max(120, Math.min(165, flowRate + (Math.random() - 0.5) * 3));
            rainfall = Math.max(10, Math.min(45, rainfall + (Math.random() - 0.5) * 1.5));
            floodLevel = Math.max(0.5, Math.min(2.8, floodLevel + (Math.random() - 0.5) * 0.08));
            pH = Math.max(6.5, Math.min(8.0, pH + (Math.random() - 0.5) * 0.05));
            waterQuality = Math.max(95, Math.min(99.5, waterQuality + (Math.random() - 0.5) * 0.2));
            waterSaved = waterSaved + (Math.random() - 0.5) * 0.03;

            if (waterSaved < 2.1) waterSaved = 2.2;
            if (waterSaved > 2.6) waterSaved = 2.5;

            const flowRateEl = document.getElementById('flow-rate');
            const rainfallEl = document.getElementById('rainfall');
            const floodLevelEl = document.getElementById('flood-level');
            const phLevelEl = document.getElementById('ph-level');
            const waterQualityEl = document.getElementById('water-quality');
            const waterSavedEl = document.getElementById('water-saved');
            const flowRateDetailEl = document.getElementById('flow-rate-detail');
            const dailyVolumeEl = document.getElementById('daily-volume');
            const rainTrendEl = document.getElementById('rain-trend');
            const rainForecastEl = document.getElementById('rain-forecast');
            const rainBarEl = document.getElementById('rain-bar');
            const floodBarEl = document.getElementById('flood-bar');
            const floodTrendEl = document.getElementById('flood-trend');
            const flowBarEl = document.getElementById('flow-bar');

            if (flowRateEl) flowRateEl.textContent = Math.round(flowRate) + ' L/s';
            if (rainfallEl) rainfallEl.textContent = Math.round(rainfall) + ' mm';
            if (floodLevelEl) floodLevelEl.textContent = floodLevel.toFixed(1) + ' m';
            if (phLevelEl) phLevelEl.textContent = pH.toFixed(1);
            if (waterQualityEl) waterQualityEl.textContent = waterQuality.toFixed(1) + '%';
            if (waterSavedEl) waterSavedEl.textContent = waterSaved.toFixed(1) + 'M';

            if (flowRateDetailEl) flowRateDetailEl.textContent = Math.round(flowRate) + ' L/s';
            if (dailyVolumeEl) dailyVolumeEl.textContent = Math.round(flowRate * 8.64) + ' m³';

            const rainTrend = ((Math.random() * 20) - 5).toFixed(0);
            if (rainTrendEl) rainTrendEl.textContent = (rainTrend >= 0 ? '+' : '') + rainTrend + '%';
            if (rainForecastEl) rainForecastEl.textContent = Math.round(rainfall - 5) + '-' + Math.round(rainfall + 8) + 'mm';
            if (rainBarEl) rainBarEl.style.width = Math.min(100, (rainfall / 50) * 100) + '%';

            const floodPercent = Math.min(100, (floodLevel / 3) * 100);
            if (floodBarEl) floodBarEl.style.width = floodPercent + '%';
            if (floodTrendEl) floodTrendEl.textContent = (floodLevel > 1.3 ? '+' : '') + (floodLevel - 1.2).toFixed(1) + 'm';

            const flowPercent = Math.min(100, (flowRate / 200) * 100);
            if (flowBarEl) flowBarEl.style.width = flowPercent + '%';

            if (chart) {
                const newFlowData = [...chart.data.datasets[0].data.slice(1), Math.round(flowRate)];
                const newQualityData = [...chart.data.datasets[1].data.slice(1), waterQuality];
                chart.data.datasets[0].data = newFlowData;
                chart.data.datasets[1].data = newQualityData;
                chart.update('none');
            }

            const alerts = [
                '<i class="fas fa-check-circle text-green-500"></i> System health: Optimal',
                '<i class="fas fa-tint text-cyan-500"></i> Flow rate stable at ' + Math.round(flowRate) + ' L/s',
                '<i class="fas fa-chart-line text-blue-500"></i> Water quality at ' + waterQuality.toFixed(1) + '%',
                '<i class="fas fa-cloud-rain text-blue-500"></i> ' + Math.round(rainfall) + 'mm rainfall recorded'
            ];

            const alertContainer = document.getElementById('alert-container');
            if (alertContainer) {
                const randomAlert = alerts[Math.floor(Math.random() * alerts.length)];
                const newAlert = document.createElement('div');
                newAlert.className = 'flex items-center gap-3 text-sm text-blue-800 animate-pulse';
                newAlert.innerHTML = randomAlert + '<span class="text-xs text-blue-500 ml-auto">Just now</span>';
                alertContainer.insertBefore(newAlert, alertContainer.firstChild);

                if (alertContainer.children.length > 4) {
                    alertContainer.removeChild(alertContainer.lastChild);
                }

                setTimeout(() => {
                    newAlert.classList.remove('animate-pulse');
                }, 1000);
            }
        }

        setInterval(simulateDataUpdate, 4000);

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

        // ========== WELCOME TOAST ==========
        setTimeout(() => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 bg-blue-800/90 backdrop-blur-md text-white px-5 py-3 rounded-xl shadow-xl z-50 flex items-center gap-2 text-sm transition-all duration-500';
            toast.innerHTML = `<i class="fas fa-chart-line text-cyan-300"></i><span>Dashboard live — monitoring ${Math.round(flowRate)} L/s flow</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }, 1500);

        console.log('🌊 AquaWatch Dashboard — Real-time monitoring active');
    </script>
</body>
</html>