<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Graph Display - AquWatch</title>
  
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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
                        <h1 class="text-3xl md:text-4xl font-extrabold text-cyan-950">Graph Display</h1>
                        <p class="text-cyan-900/80 mt-1">Simple flow, rain, and flood trend view for quick decisions</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-4 shadow-lg">
                <p class="text-xs uppercase tracking-wide text-cyan-900/70 mb-3">Sensor Categories</p>
                <div class="flex flex-wrap gap-2">
                    <a href="#flow-graph-section" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition sensor-nav-link">
                        <i class="fas fa-tint mr-1"></i> Flow Sensor Graph
                    </a>
                    <a href="#rain-graph-section" class="px-4 py-2 rounded-xl bg-white/70 text-cyan-900 hover:bg-white transition sensor-nav-link">
                        <i class="fas fa-cloud-rain mr-1"></i> Rain Sensor Graph
                    </a>
                    <a href="#flood-graph-section" class="px-4 py-2 rounded-xl bg-white/70 text-cyan-900 hover:bg-white transition sensor-nav-link">
                        <i class="fas fa-water mr-1"></i> Flood Sensor Graph
                    </a>
                </div>
            </div>

            <div id="flow-graph-section" class="glass-card rounded-3xl p-4 md:p-6 shadow-xl scroll-mt-24">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-cyan-950">Flow Trend</h2>
                    <div class="flex gap-2">
                        <button id="btn-hour" class="px-3 py-1.5 rounded-lg bg-cyan-700 text-white text-sm">Last 1 hour</button>
                        <button id="btn-day" class="px-3 py-1.5 rounded-lg bg-white/70 text-cyan-900 text-sm">Last 24 hours</button>
                    </div>
                </div>
                <div class="h-80 md:h-96">
                    <canvas id="flowTrendChart"></canvas>
                </div>
                <p id="live-status" class="mt-4 text-sm text-cyan-900/80">Live: updating every 5 seconds</p>
            </div>

            <div id="rain-graph-section" class="glass-card rounded-3xl p-4 md:p-6 shadow-xl scroll-mt-24">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-cyan-950">Rain Condition Trend</h2>
                    <div class="text-sm text-cyan-900/80">Uses same time range buttons above</div>
                </div>
                <div class="h-80 md:h-96">
                    <canvas id="rainTrendChart"></canvas>
                </div>
                <p id="rain-live-status" class="mt-4 text-sm text-cyan-900/80">Live: updating every 5 seconds</p>
            </div>

            <div id="flood-graph-section" class="glass-card rounded-3xl p-4 md:p-6 shadow-xl scroll-mt-24">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-xl font-bold text-cyan-950">Flood Status Trend</h2>
                    <div class="text-sm text-cyan-900/80">Uses same time range buttons above</div>
                </div>
                <div class="h-80 md:h-96">
                    <canvas id="floodTrendChart"></canvas>
                </div>
                <p id="flood-live-status" class="mt-4 text-sm text-cyan-900/80">Live: updating every 5 seconds</p>
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
        document.documentElement.style.scrollBehavior = 'smooth';

        const initialPayload = @json($initialPayload);
        const dataUrl = @json(route('contents.graph-display.data'));

        let currentMode = 'hour';

        const chartContext = document.getElementById('flowTrendChart').getContext('2d');
        const flowTrendChart = new Chart(chartContext, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Flow (L/min)',
                        data: [],
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.20)',
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

        const rainChartContext = document.getElementById('rainTrendChart').getContext('2d');
        const rainSeverityBandsPlugin = {
            id: 'rainSeverityBands',
            beforeDraw(chart) {
                const yScale = chart?.scales?.y;
                const xScale = chart?.scales?.x;

                if (!yScale || !xScale) {
                    return;
                }

                const bands = [
                    { start: 0, end: 1, color: 'rgba(56, 189, 248, 0.12)' },
                    { start: 1, end: 2, color: 'rgba(59, 130, 246, 0.14)' },
                ];

                const left = xScale.left;
                const right = xScale.right;
                const width = right - left;

                chart.ctx.save();
                for (const band of bands) {
                    const top = yScale.getPixelForValue(band.end);
                    const bottom = yScale.getPixelForValue(band.start);
                    chart.ctx.fillStyle = band.color;
                    chart.ctx.fillRect(left, top, width, bottom - top);
                }
                chart.ctx.restore();
            },
        };

        const rainTrendChart = new Chart(rainChartContext, {
            type: 'line',
            plugins: [rainSeverityBandsPlugin],
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Rain Condition',
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
                        min: 0,
                        max: 2,
                        ticks: {
                            color: '#0f172a',
                            stepSize: 1,
                            callback: function(value) {
                                if (Number(value) === 2) return 'Heavy Rain';
                                if (Number(value) === 1) return 'Rain';
                                return 'No Rain';
                            },
                        },
                        grid: { color: 'rgba(15, 23, 42, 0.12)' },
                    },
                },
                plugins: {
                    legend: { labels: { color: '#083344' } },
                },
            },
        });

        const floodChartContext = document.getElementById('floodTrendChart').getContext('2d');
        const floodSeverityBandsPlugin = {
            id: 'floodSeverityBands',
            beforeDraw(chart) {
                const yScale = chart?.scales?.y;
                const xScale = chart?.scales?.x;

                if (!yScale || !xScale) {
                    return;
                }

                const bands = [
                    { start: 0, end: 1, color: 'rgba(34, 197, 94, 0.12)' },
                    { start: 1, end: 2, color: 'rgba(234, 179, 8, 0.12)' },
                    { start: 2, end: 3, color: 'rgba(249, 115, 22, 0.12)' },
                    { start: 3, end: 4, color: 'rgba(239, 68, 68, 0.12)' },
                ];

                const left = xScale.left;
                const right = xScale.right;
                const width = right - left;

                chart.ctx.save();
                for (const band of bands) {
                    const top = yScale.getPixelForValue(band.end);
                    const bottom = yScale.getPixelForValue(band.start);
                    chart.ctx.fillStyle = band.color;
                    chart.ctx.fillRect(left, top, width, bottom - top);
                }
                chart.ctx.restore();
            },
        };

        const floodTrendChart = new Chart(floodChartContext, {
            type: 'line',
            plugins: [floodSeverityBandsPlugin],
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Flood Severity',
                        data: [],
                        borderColor: '#0c4a6e',
                        backgroundColor: 'rgba(12, 74, 110, 0.20)',
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
                        min: 0,
                        max: 4,
                        ticks: {
                            color: '#0f172a',
                            stepSize: 1,
                            callback: function(value) {
                                if (Number(value) === 4) return 'Critical';
                                if (Number(value) === 3) return 'Flash Warning';
                                if (Number(value) === 2) return 'Normal Rise';
                                if (Number(value) === 1) return 'Level 1';
                                return 'Safe';
                            },
                        },
                        grid: { color: 'rgba(15, 23, 42, 0.12)' },
                    },
                },
                plugins: {
                    legend: { labels: { color: '#083344' } },
                },
            },
        });

        function rainStatusLabel(level) {
            if (level === 'heavy_rain') return 'Heavy Rain';
            if (level === 'rain') return 'Rain';
            return 'No Rain';
        }

        function rainStatusScore(level) {
            if (level === 'heavy_rain') return 2;
            if (level === 'rain') return 1;
            return 0;
        }

        function floodStatusLabel(status) {
            if (status === 'CRITICAL') return 'Critical';
            if (status === 'FLASH FLOOD WARNING') return 'Flash Flood Warning';
            if (status === 'NORMAL RISE') return 'Normal Rise';
            if (status === 'LEVEL 1 DETECTED') return 'Level 1 Detected';
            return 'Safe / Dry';
        }

        function floodStatusScore(status) {
            if (status === 'CRITICAL') return 4;
            if (status === 'FLASH FLOOD WARNING') return 3;
            if (status === 'NORMAL RISE') return 2;
            if (status === 'LEVEL 1 DETECTED') return 1;
            return 0;
        }

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
            const flowSeries = payload?.flow?.series?.[currentMode] ?? [];

            flowTrendChart.data.labels = flowSeries.map((point) => formatChartTime(point.measured_at, currentMode));
            flowTrendChart.data.datasets[0].data = flowSeries.map((point) => Number(point.flow_lpm ?? 0));
            flowTrendChart.update();

            const rainLatest = payload?.rain?.latest;
            const rainSeries = payload?.rain?.series?.[currentMode] ?? [];
            const rainLevel = String(rainLatest?.intensity_level ?? 'no_rain');

            rainTrendChart.data.labels = rainSeries.map((point) => formatChartTime(point.measured_at, currentMode));
            rainTrendChart.data.datasets[0].data = rainSeries.map((point) => rainStatusScore(String(point.intensity_level ?? 'no_rain')));
            rainTrendChart.data.datasets[0].label = 'Rain Condition (' + rainStatusLabel(rainLevel) + ')';
            rainTrendChart.update();

            const floodLatest = payload?.flood?.latest;
            const floodSeries = payload?.flood?.series?.[currentMode] ?? [];
            const floodStatus = String(floodLatest?.status ?? 'SAFE / DRY');

            floodTrendChart.data.labels = floodSeries.map((point) => formatChartTime(point.measured_at, currentMode));
            floodTrendChart.data.datasets[0].data = floodSeries.map((point) => floodStatusScore(String(point.status ?? 'SAFE / DRY')));
            floodTrendChart.data.datasets[0].label = 'Flood Severity (' + floodStatusLabel(floodStatus) + ')';
            floodTrendChart.update();
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
                document.getElementById('rain-live-status').textContent = 'Live: connected';
                document.getElementById('flood-live-status').textContent = 'Live: connected';
            } catch {
                document.getElementById('live-status').textContent = 'Live: reconnecting...';
                document.getElementById('rain-live-status').textContent = 'Live: reconnecting...';
                document.getElementById('flood-live-status').textContent = 'Live: reconnecting...';
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