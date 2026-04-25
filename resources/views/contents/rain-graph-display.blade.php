<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rain Graph Display - AquWatch</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                        <h1 class="text-3xl md:text-4xl font-extrabold text-cyan-950">Rain Graph Display</h1>
                        <p class="text-cyan-900/80 mt-1">Rain sensor trend view for the last hour and day</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('contents.rain-display') }}" class="px-4 py-2 rounded-xl bg-white/70 text-cyan-900 hover:bg-white transition">
                            Summary View
                        </a>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Sensor Value Now</p>
                    <p id="kpi-value" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">analog value</p>
                </div>
                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Average (1 hour)</p>
                    <p id="kpi-hour-avg" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">analog value</p>
                </div>
                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Heavy Rain Events (1 hour)</p>
                    <p id="kpi-heavy-count" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">count</p>
                </div>
            </div>

            <div class="glass-card rounded-3xl p-4 md:p-6 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-cyan-950">Rain Trend</h2>
                    <div class="flex gap-2">
                        <button id="btn-hour" class="px-3 py-1.5 rounded-lg bg-cyan-700 text-white text-sm">Last 1 hour</button>
                        <button id="btn-day" class="px-3 py-1.5 rounded-lg bg-white/70 text-cyan-900 text-sm">Last 24 hours</button>
                    </div>
                </div>
                <div class="h-80 md:h-96">
                    <canvas id="rainTrendChart"></canvas>
                </div>
                <p id="live-status" class="mt-4 text-sm text-cyan-900/80">Live: updating every 5 seconds</p>
            </div>
        </div>
    </main>

    <footer class="mt-auto relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="https://x.com/AquWatch" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/aquwatch/" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition"><i class="fab fa-instagram"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch — Protecting our waters
        </p>
    </footer>

    <script>
        const initialPayload = @json($initialPayload);
        const dataUrl = @json(route('contents.rain-graph-display.data'));

        let currentMode = 'hour';

        function levelColor(level) {
            if (level === 'heavy_rain') return '#4338ca';
            if (level === 'rain') return '#0369a1';
            return '#0f766e';
        }

        function toReadableLevel(level) {
            if (level === 'heavy_rain') return 'Heavy Rain';
            if (level === 'rain') return 'Rain';
            return 'No Rain';
        }

        const chartContext = document.getElementById('rainTrendChart').getContext('2d');
        const rainTrendChart = new Chart(chartContext, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Rain Analog Value',
                        data: [],
                        borderColor: '#0369a1',
                        backgroundColor: 'rgba(3, 105, 161, 0.20)',
                        borderWidth: 3,
                        pointRadius: 1.5,
                        fill: true,
                        tension: 0.30,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { color: '#0f172a' },
                        grid: { color: 'rgba(15, 23, 42, 0.12)' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#0f172a' },
                        grid: { color: 'rgba(15, 23, 42, 0.12)' },
                    },
                },
                plugins: {
                    legend: { labels: { color: '#083344' } },
                },
            },
        });

        function formatChartTime(iso, mode) {
            const date = new Date(iso);
            if (Number.isNaN(date.getTime())) {
                return '-';
            }

            return mode === 'day'
                ? date.toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
                : date.toLocaleTimeString();
        }

        function applyModeButtons(mode) {
            document.getElementById('btn-hour').className = mode === 'hour'
                ? 'px-3 py-1.5 rounded-lg bg-cyan-700 text-white text-sm'
                : 'px-3 py-1.5 rounded-lg bg-white/70 text-cyan-900 text-sm';

            document.getElementById('btn-day').className = mode === 'day'
                ? 'px-3 py-1.5 rounded-lg bg-cyan-700 text-white text-sm'
                : 'px-3 py-1.5 rounded-lg bg-white/70 text-cyan-900 text-sm';
        }

        function render(payload) {
            const latest = payload?.latest;
            const latestLevel = String(latest?.intensity_level ?? 'no_rain');

            document.getElementById('kpi-value').textContent = Number(latest?.analog_value ?? 0).toLocaleString();
            document.getElementById('kpi-hour-avg').textContent = Number(payload?.stats?.hour_avg_analog ?? 0).toFixed(1);
            document.getElementById('kpi-heavy-count').textContent = Number(payload?.stats?.hour_heavy_count ?? 0);

            const series = payload?.series?.[currentMode] ?? [];
            rainTrendChart.data.labels = series.map((point) => formatChartTime(point.measured_at, currentMode));
            rainTrendChart.data.datasets[0].data = series.map((point) => Number(point.analog_value ?? 0));
            rainTrendChart.data.datasets[0].borderColor = levelColor(latestLevel);
            rainTrendChart.data.datasets[0].backgroundColor = latestLevel === 'heavy_rain'
                ? 'rgba(67, 56, 202, 0.20)'
                : latestLevel === 'rain'
                    ? 'rgba(3, 105, 161, 0.20)'
                    : 'rgba(15, 118, 110, 0.20)';
            rainTrendChart.data.datasets[0].label = 'Rain Analog Value (' + toReadableLevel(latestLevel) + ')';
            rainTrendChart.update();
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
                document.getElementById('live-status').textContent = 'Live: connected';
            } catch {
                document.getElementById('live-status').textContent = 'Live: reconnecting...';
            }
        }

        document.getElementById('btn-hour').addEventListener('click', async () => {
            currentMode = 'hour';
            applyModeButtons(currentMode);
            await refreshData();
        });

        document.getElementById('btn-day').addEventListener('click', async () => {
            currentMode = 'day';
            applyModeButtons(currentMode);
            await refreshData();
        });

        applyModeButtons(currentMode);
        render(initialPayload);
        setInterval(refreshData, 5000);

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
