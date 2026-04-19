<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Flow Monitoring - AquWatch</title>
  
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
                        <h1 class="text-3xl md:text-4xl font-extrabold text-cyan-950">Water Flow Monitor</h1>
                        <p class="text-cyan-900/80 mt-1">Simple live view of your water flow and total usage</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Flow Right Now</p>
                    <p id="kpi-flow" class="text-3xl font-bold text-cyan-950 mt-1">0.000</p>
                    <p class="text-cyan-900/70 text-sm">L/min</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Total Water Measured</p>
                    <p id="kpi-total" class="text-3xl font-bold text-cyan-950 mt-1">0</p>
                    <p class="text-cyan-900/70 text-sm">mL</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Water Status</p>
                    <p id="kpi-status" class="text-2xl font-bold text-cyan-950 mt-1">Checking...</p>
                    <p id="kpi-sensor" class="text-cyan-900/70 text-sm">Sensor: -</p>
                </div>

                <div class="glass-card rounded-2xl p-4 shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-cyan-900/70">Last Updated</p>
                    <p id="kpi-time" class="text-lg font-bold text-cyan-950 mt-1">-</p>
                    <p class="text-cyan-900/70 text-sm">time of latest reading</p>
                </div>
            </div>

            <div class="glass-card rounded-3xl p-4 md:p-6 shadow-xl">
                <h2 class="text-xl font-bold text-cyan-950 mb-3">More Details</h2>
                <p class="text-cyan-900/80 mb-4">Need charts or full reading history? Open one of the optional pages below.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('contents.graph-display') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">
                        Open Graph Display
                    </a>
                    <a href="{{ route('contents.flow-readings') }}" class="px-4 py-2 rounded-xl bg-white/70 text-cyan-900 hover:bg-white transition">
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
        const dataUrl = @json(route('contents.flow-display.data'));

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
            const flowLpm = Number(latest?.flow_lpm ?? 0);
            const LOW_FLOW_THRESHOLD = 0.2;
            const HIGH_FLOW_THRESHOLD = 3.0;
            document.getElementById('kpi-flow').textContent = flowLpm.toFixed(3);
            document.getElementById('kpi-total').textContent = Number(latest?.total_ml ?? 0).toLocaleString();
            document.getElementById('kpi-sensor').textContent = `Sensor: ${latest?.sensor_id ?? '-'}`;
            document.getElementById('kpi-time').textContent = formatTime(latest?.measured_at);

            const statusElement = document.getElementById('kpi-status');
            if (!latest?.is_recent) {
                statusElement.textContent = 'No recent data';
                statusElement.className = 'text-2xl font-bold text-slate-700 mt-1';
            } else if (flowLpm < LOW_FLOW_THRESHOLD) {
                statusElement.textContent = 'Low Water Flow';
                statusElement.className = 'text-2xl font-bold text-amber-700 mt-1';
            } else if (flowLpm <= HIGH_FLOW_THRESHOLD) {
                statusElement.textContent = 'Normal Flow';
                statusElement.className = 'text-2xl font-bold text-emerald-700 mt-1';
            } else {
                statusElement.textContent = 'Too Much Flow';
                statusElement.className = 'text-2xl font-bold text-rose-700 mt-1';
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
                const statusElement = document.getElementById('kpi-status');
                statusElement.textContent = 'Connection issue';
                statusElement.className = 'text-2xl font-bold text-rose-700 mt-1';
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