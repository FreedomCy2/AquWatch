<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Rain Display - AquWatch</title>
  
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        .wave-bg {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .wave-svg {
            width: 100%;
            height: 100px;
        }

        .bubble {
            position: fixed;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
            animation: floatUp linear infinite;
        }

        @keyframes floatUp {
            0% { transform: translateY(0); opacity: 0.7; }
            100% { transform: translateY(-100vh); opacity: 0; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.32);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.45);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <div class="wave-bg">
        <svg class="wave-svg" viewBox="0 0 1440 320">
            <path fill="#4fc3f7" fill-opacity="0.7"
                d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z">
            </path>
        </svg>
    </div>

    <main class="flex-grow relative z-10 p-5 md:p-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="glass-card rounded-3xl p-5 md:p-7 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-cyan-950">Rain Monitor</h1>
                        <p class="text-cyan-900/80 mt-1">Live rain sensor status from ESP32 analog readings</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Rain Status</p>
                    <p id="kpi-status" class="text-2xl font-bold text-cyan-950 mt-1">Checking...</p>
                    <p class="text-cyan-900/70 text-sm">no rain / rain / heavy rain</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Sensor Value</p>
                    <p id="kpi-analog" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">analog (0-4095)</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Last Updated</p>
                    <p id="kpi-time" class="text-lg font-bold text-cyan-950 mt-1">-</p>
                    <p id="kpi-sensor" class="text-cyan-900/70 text-sm">Sensor: -</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Last Hour</p>
                    <p id="kpi-hour" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">rain samples</p>
                </div>
            </div>

            <div class="glass-card rounded-3xl p-4 md:p-6 shadow-xl">
                <h2 class="text-xl font-bold text-cyan-950 mb-3">More Details</h2>
                <p class="text-cyan-900/80 mb-4">Need charts or full reading history? Open one of the optional pages below.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('contents.rain-graph-display') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                        Open Graph Display
                    </a>
                    <a href="{{ route('contents.rain-readings') }}" class="px-4 py-2 rounded-xl bg-white/70 text-cyan-900 hover:bg-white transition">
                        View Recent Readings
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-auto relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-github"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch — Protecting our waters
        </p>
    </footer>

    <script>
        const initialPayload = @json($initialPayload);
        const dataUrl = @json(route('contents.rain-display.data'));

        function toReadableLevel(level) {
            if (level === 'heavy_rain') return 'Heavy Rain';
            if (level === 'rain') return 'Rain';
            if (level === 'no_rain') return 'No Rain';
            return 'Unknown';
        }

        function formatTime(iso) {
            if (!iso) {
                return '-';
            }

            const date = new Date(iso);
            if (Number.isNaN(date.getTime())) {
                return '-';
            }

            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function render(payload) {
            const latest = payload?.latest;
            document.getElementById('kpi-analog').textContent = Number(latest?.analog_value ?? 0);
            document.getElementById('kpi-time').textContent = formatTime(latest?.measured_at);
            document.getElementById('kpi-sensor').textContent = `Sensor: ${latest?.sensor_id ?? '-'}`;
            document.getElementById('kpi-hour').textContent = Number(payload?.stats?.last_hour_rain_count ?? 0);

            const statusEl = document.getElementById('kpi-status');
            if (!latest?.is_recent) {
                statusEl.textContent = 'No recent data';
                statusEl.className = 'text-2xl font-bold text-slate-700 mt-1';
            } else if (latest?.intensity_level === 'heavy_rain') {
                statusEl.textContent = 'Heavy Rain';
                statusEl.className = 'text-2xl font-bold text-indigo-700 mt-1';
            } else if (latest?.intensity_level === 'rain') {
                statusEl.textContent = 'Rain';
                statusEl.className = 'text-2xl font-bold text-blue-700 mt-1';
            } else {
                statusEl.textContent = 'No Rain';
                statusEl.className = 'text-2xl font-bold text-emerald-700 mt-1';
            }
        }

        async function refreshData() {
            try {
                const response = await fetch(dataUrl, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json();
                render(payload);
            } catch {
                const statusEl = document.getElementById('kpi-status');
                statusEl.textContent = 'Connection issue';
                statusEl.className = 'text-2xl font-bold text-rose-700 mt-1';
            }
        }

        render(initialPayload);
        setInterval(refreshData, 3000);

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
            
            setTimeout(() => {
                if (bubble && bubble.remove) bubble.remove();
            }, 10000);
        }
        
        setInterval(createBubble, 380);
        for (let i = 0; i < 12; i++) setTimeout(createBubble, i * 200);
    </script>

</body>
</html>