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
    </style>
</head>

<!-- ✅ FIXED BODY -->
<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">

    <!-- Bubble Background -->
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <!-- Wave Background -->
    <div class="wave-bg">
        <svg class="wave-svg" viewBox="0 0 1440 320">
            <path fill="#4fc3f7" fill-opacity="0.7"
                d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z">
            </path>
        </svg>
    </div>

    <!-- ✅ MAIN CONTENT (IMPORTANT) -->
    <main class="flex-grow relative z-10 p-6">
        <!-- You can put your dashboard content here -->
        <div class="text-center text-blue-900 mt-10">
            <h1 class="text-3xl font-bold">Flow Display</h1>
            <p class="text-sm opacity-70">Your content goes here</p>
        </div>
    </main>

    <!-- ✅ FIXED FOOTER -->
    <footer class="mt-auto relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="hover:text-cyan-800 transition"><i class="fab fa-github"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquaWatch — Protecting our waters
        </p>
    </footer>

    <script>
        // ---------- INTERACTIVE BUBBLE GENERATION (ocean vibe) ----------
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
        
        // Interactive live stats random updater (to simulate real-time)
        let qualityIndex = 98.4;
        let activeUsers = 1284;
        let savedLitersVal = 2.3;
        
        function updateLiveStats() {
            // Simulate small fluctuations for interactivity
            qualityIndex = +(qualityIndex + (Math.random() - 0.5) * 0.3).toFixed(1);
            if (qualityIndex > 99.5) qualityIndex = 99.2;
            if (qualityIndex < 96.0) qualityIndex = 96.8;
            
            const qualityEl = document.getElementById('live-water-quality');
            if (qualityEl) qualityEl.innerText = qualityIndex + '%';
            
            // active users gently increase
            activeUsers += Math.floor(Math.random() * 3) - 1;
            if (activeUsers < 1100) activeUsers = 1120;
            if (activeUsers > 1450) activeUsers = 1430;
            const userEl = document.getElementById('active-users-counter');
            if (userEl) userEl.innerText = activeUsers.toLocaleString();
            
            // saved liters increment randomly
            savedLitersVal += (Math.random() * 0.08);
            if (savedLitersVal > 2.9) savedLitersVal = 2.4;
            const litersEl = document.getElementById('saved-liters');
            if (litersEl) litersEl.innerText = savedLitersVal.toFixed(1) + 'M';
        }
        
        setInterval(updateLiveStats, 3800);
        
        // Add ripple effect to all buttons with class .ripple-effect dynamically
        document.querySelectorAll('.ripple-effect, .glow-button, .card-hover').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const rippleDiv = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size/2;
                const y = e.clientY - rect.top - size/2;
                rippleDiv.style.width = rippleDiv.style.height = size + 'px';
                rippleDiv.style.position = 'absolute';
                rippleDiv.style.top = y + 'px';
                rippleDiv.style.left = x + 'px';
                rippleDiv.style.background = 'radial-gradient(circle, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 80%)';
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
        
        // Feature alert interactive
        window.showFeatureAlert = (feature) => {
            // Create a toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-blue-800/90 backdrop-blur-lg text-white px-6 py-3 rounded-2xl shadow-2xl z-50 flex items-center gap-3 animate-bounce transition-all duration-500';
            toast.innerHTML = `<i class="fas fa-info-circle text-cyan-200 text-xl"></i><span class="font-medium">✨ ${feature} feature — dive into smart water monitoring! ✨</span><i class="fas fa-water text-cyan-200"></i>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 2800);
        };
        
        // Add floating effect to logo on hover + interactive card stat simulation
        const logoImg = document.querySelector('img[alt="AquaWatch Logo"]');
        if (logoImg) {
            logoImg.addEventListener('mouseenter', () => {
                logoImg.style.filter = 'drop-shadow(0 10px 18px rgba(0,150,180,0.5))';
            });
            logoImg.addEventListener('mouseleave', () => {
                logoImg.style.filter = 'drop-shadow(0 4px 8px rgba(0,0,0,0.1))';
            });
        }
        
        // Custom interactive mouse move on hero card (parallax light effect)
        const heroCard = document.querySelector('.interactive-card');
        if (heroCard) {
            heroCard.addEventListener('mousemove', (e) => {
                const rect = heroCard.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width;
                const y = (e.clientY - rect.top) / rect.height;
                const shadowX = (x - 0.5) * 20;
                const shadowY = (y - 0.5) * 20;
                heroCard.style.boxShadow = `${shadowX}px ${shadowY}px 40px rgba(0, 100, 130, 0.3)`;
                const glow = heroCard.querySelector('.bg-blue-400/20');
                if (glow) {
                    glow.style.transform = `translate(${shadowX * 0.2}px, ${shadowY * 0.2}px)`;
                }
            });
            heroCard.addEventListener('mouseleave', () => {
                heroCard.style.boxShadow = '';
            });
        }
        
        // additional interactive wave text - update footer year
        const footerYearSpan = document.querySelector('footer p');
        if (footerYearSpan) {
            // Already dynamic year in place, no action needed but ensure copyright is current
        }
        
        // add interactive tooltip to stats
        const statCards = document.querySelectorAll('.float-stat > div');
        statCards.forEach(stat => {
            stat.classList.add('cursor-help');
            stat.setAttribute('title', 'Live ocean data simulation');
        });
        
        // live water quality mini interactive
        console.log('🌊 AquaWatch interactive ocean theme active — realtime bubbles & stats');
    </script>

</body>
</html>