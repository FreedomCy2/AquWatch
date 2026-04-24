<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquWatch | Intelligent Water Monitoring</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons (for interactive icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts for elegance -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom wave animations & interactive elements */
        .wave-bg {
            position: absolute;
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
            height: 120px;
            animation: gentleWave 8s ease-in-out infinite alternate;
        }
        
        @keyframes gentleWave {
            0% { transform: translateX(0px) translateY(0px); }
            100% { transform: translateX(-15px) translateY(5px); }
        }
        
        .bubble {
            position: absolute;
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
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 35px -12px rgba(0, 80, 100, 0.4);
        }
        
        .glow-button {
            transition: all 0.25s ease;
            box-shadow: 0 8px 20px rgba(0, 100, 120, 0.3);
        }
        
        .glow-button:hover {
            box-shadow: 0 15px 28px rgba(0, 140, 160, 0.5);
            transform: scale(1.03);
        }
        
        .interactive-card {
            backdrop-filter: blur(2px);
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.4s;
        }
        
        .interactive-card:hover {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
        }
        
        .ripple-effect {
            position: relative;
            overflow: hidden;
        }
        
        .ripple-effect:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(0,150,200,0.2) 10%, transparent 10%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10);
            opacity: 0;
            transition: transform 0.5s, opacity 0.8s;
        }
        
        .ripple-effect:active:after {
            transform: scale(0);
            opacity: 0.4;
            transition: 0s;
        }
        
        /* floating stats animation */
        .float-stat {
            animation: softFloat 3s infinite alternate ease-in-out;
        }
        
        @keyframes softFloat {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-8px); }
        }
        
        /* custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #0f6b7c;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 min-h-screen flex flex-col relative overflow-x-hidden">

    <!-- === DYNAMIC BUBBLES (Javascript generated) === -->
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>
    
    <!-- Animated Wave Background (bottom) -->
    <div class="wave-bg">
        <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
    
    <!-- Header with interactive menu -->
    <header class="relative z-20 w-full max-w-6xl mx-auto flex flex-col gap-4 py-6 px-4 sm:px-6 md:px-8 md:flex-row md:justify-between md:items-center">
        <div class="flex items-center gap-2 group cursor-pointer transition-all duration-300 hover:scale-105 self-start min-w-0">
<img src="{{ asset('images/logo.png') }}" 
     alt="AquWatch Logo"
     class="h-10 w-auto drop-shadow-md">
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight bg-gradient-to-r from-blue-800 to-teal-700 bg-clip-text text-transparent truncate">AquWatch</h1>
        </div>
        
        @if (Route::has('login'))
            <nav class="w-full md:w-auto flex flex-wrap items-center gap-2 sm:gap-3 md:gap-5">
                <a href="{{ route('plans') }}"
                   class="ripple-effect px-4 py-2.5 bg-white/70 backdrop-blur-sm text-blue-800 border border-blue-300 rounded-xl hover:bg-white hover:shadow-md transition-all duration-300 font-medium flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap">
                    <i class="fas fa-crown"></i> Upgrade
                </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="ripple-effect px-4 py-2.5 bg-gradient-to-r from-blue-600 to-teal-600 text-white rounded-xl shadow-lg hover:from-blue-700 hover:to-teal-700 transition-all duration-300 font-medium flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="ripple-effect px-4 py-2.5 bg-white/70 backdrop-blur-sm text-blue-800 border border-blue-300 rounded-xl hover:bg-white hover:shadow-md transition-all duration-300 font-medium flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap">
                        <i class="fas fa-sign-in-alt"></i> Log in
                    </a>
                    
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="ripple-effect px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>
    
    <!-- Main interactive content -->
    <main class="relative z-10 flex flex-1 items-center justify-center px-6 py-10">
        <div class="max-w-4xl w-full">
            <!-- Hero card with interactive elements -->
            <div class="interactive-card rounded-3xl shadow-2xl p-8 md:p-12 text-center backdrop-blur-sm border border-white/50 transition-all duration-500">
                
                <!-- Animated Logo with pulse effect -->
                <div class="relative flex justify-center mb-6 group">
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full blur-xl scale-110 group-hover:scale-125 transition-transform duration-700"></div>
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="AquWatch Logo"
                         class="relative w-36 h-36 md:w-40 md:h-40 object-contain rounded-2xl shadow-2xl border-4 border-white/60 transition-all duration-500 group-hover:rotate-2 group-hover:scale-105">
                </div>
                
                <!-- Title with dynamic typing effect -->
                <div class="mb-4">
                    <h2 class="text-4xl md:text-6xl font-black bg-gradient-to-r from-blue-900 via-cyan-800 to-teal-800 bg-clip-text text-transparent tracking-tight">
                        Welcome to AquWatch
                    </h2>
                    <div class="h-2 w-24 bg-gradient-to-r from-cyan-400 to-blue-500 mx-auto rounded-full mt-3 animate-pulse"></div>
                </div>
                
                <!-- Interactive description with floating icon -->
                <p class="text-blue-900/80 text-lg md:text-xl mb-6 max-w-lg mx-auto flex items-center justify-center gap-2 flex-wrap">
                    <i class="fas fa-tint text-cyan-600 animate-bounce"></i>
                    Monitor your water system easily, securely, and in real-time.
                    <i class="fas fa-charging-station text-teal-600 animate-pulse"></i>
                </p>
                
                <!-- Live indicator badge (interactive) -->
                <div class="inline-flex items-center gap-2 bg-white/50 backdrop-blur-sm rounded-full px-4 py-1.5 mb-8 shadow-sm border border-cyan-200">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-semibold text-cyan-800 tracking-wide">LIVE MONITORING ACTIVE</span>
                    <i class="fas fa-waveform text-cyan-600 ml-1"></i>
                </div>
                
                @guest
                    <!-- Interactive call to action buttons with icons and micro-interactions -->
                    <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-5 items-stretch sm:items-center">
                        <a href="{{ route('plans') }}"
                           class="group w-full sm:w-auto justify-center px-6 sm:px-8 py-4 bg-white/90 backdrop-blur-sm text-blue-800 border-2 border-blue-300 rounded-2xl shadow-xl hover:shadow-2xl hover:border-teal-400 transition-all duration-300 font-bold text-base sm:text-lg flex items-center gap-3">
                            <i class="fas fa-crown text-amber-500 group-hover:scale-110 transition-transform"></i>
                            View Plans
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="glow-button group relative w-full sm:w-auto justify-center px-6 sm:px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-2xl shadow-2xl hover:shadow-blue-500/40 transition-all duration-300 font-bold text-base sm:text-lg flex items-center gap-3 overflow-hidden">
                            <i class="fas fa-water group-hover:animate-wiggle text-xl"></i>
                            <span>Get Started</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </a>
                        
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                         class="group w-full sm:w-auto justify-center px-6 sm:px-8 py-4 bg-white/90 backdrop-blur-sm text-blue-800 border-2 border-blue-300 rounded-2xl shadow-xl hover:shadow-2xl hover:border-teal-400 transition-all duration-300 font-bold text-base sm:text-lg flex items-center gap-3">
                                <i class="fas fa-id-card group-hover:scale-110 transition-transform"></i>
                                Create Account
                                <i class="fas fa-plus-circle text-sm opacity-70 group-hover:opacity-100"></i>
                            </a>
                        @endif
                    </div>
                @endguest
                
                @auth
                    <div class="flex justify-center">
                        <a href="{{ route('dashboard') }}"
                           class="w-full sm:w-auto justify-center px-8 sm:px-10 py-4 bg-gradient-to-r from-teal-500 to-blue-600 text-white rounded-2xl shadow-2xl hover:scale-105 transition-all duration-300 font-bold text-lg sm:text-xl flex items-center gap-3">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    </div>
                @endauth
            </div>
            
            <!-- Interactive feature cards - only visible for extra engagement -->
            <div class="grid md:grid-cols-3 gap-6 mt-12">
                <div class="card-hover bg-white/40 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/50 transition-all cursor-pointer" onclick="showFeatureAlert('Real-time Monitoring')">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-blue-900 text-lg">Live Metrics</h3>
                    <p class="text-blue-800/80 text-sm mt-1">pH, turbidity & flow in real-time</p>
                    <span class="text-xs text-cyan-700 mt-2 inline-block opacity-70 group-hover:opacity-100"><i class="fas fa-mouse-pointer"></i> click me</span>
                </div>
                <div class="card-hover bg-white/40 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/50 transition-all cursor-pointer" onclick="showFeatureAlert('Smart Alerts')">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-bell text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-blue-900 text-lg">Instant Alerts</h3>
                    <p class="text-blue-800/80 text-sm mt-1">Get notified on anomalies</p>
                    <span class="text-xs text-cyan-700 mt-2 inline-block opacity-70"><i class="fas fa-mouse-pointer"></i> click me</span>
                </div>
                <div class="card-hover bg-white/40 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/50 transition-all cursor-pointer" onclick="showFeatureAlert('Eco Insights')">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-400 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-leaf text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-blue-900 text-lg">Sustainability</h3>
                    <p class="text-blue-800/80 text-sm mt-1">Reduce waste, save water</p>
                    <span class="text-xs text-cyan-700 mt-2 inline-block opacity-70"><i class="fas fa-mouse-pointer"></i> click me</span>
                </div>
            </div>
            
            <!-- interactive ocean stats (floating stats card) -->
            <div class="mt-12 flex justify-center">
                <div class="float-stat bg-white/30 backdrop-blur-md rounded-2xl px-6 py-3 inline-flex gap-6 md:gap-10 border border-white/60 shadow-lg flex-wrap justify-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-droplet text-cyan-700 text-xl"></i>
                        <span class="text-blue-900 font-semibold"><span id="live-water-quality" class="text-teal-700">98.4%</span> purity index</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-blue-700 text-xl"></i>
                        <span class="text-blue-900 font-semibold"><span id="active-users-counter">1,284</span> active monitors</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-chart-simple text-cyan-800 text-xl"></i>
                        <span class="text-blue-900 font-semibold"><span id="saved-liters">2.3M</span> L saved</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Dynamic Footer with ripple effect -->
    <footer class="relative z-10 text-center text-blue-900/80 py-6 text-sm backdrop-blur-sm bg-white/20 mt-12 border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="#" class="hover:text-cyan-800 transition-colors duration-200"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-colors duration-200"><i class="fab fa-linkedin"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-colors duration-200"><i class="fab fa-github"></i></a>
        </div>
        <p>© 2025 AquWatch — Intelligent Ocean Conservation · <span class="inline-flex items-center"><i class="fas fa-heart text-rose-400 text-xs mx-1"></i> protect our waves</span></p>
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
        const logoImg = document.querySelector('img[alt="AquWatch Logo"]');
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
        console.log('🌊 AquWatch interactive ocean theme active — realtime bubbles & stats');
    </script>
    
    <!-- Additional inline style for custom animation wiggle -->
    <style>
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }
        .group:hover .fa-water {
            animation: wiggle 0.3s ease-in-out;
        }
        .fa-water, .fa-tint, .fa-chart-line {
            transition: all 0.2s;
        }
        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }
    </style>
</body>
</html>