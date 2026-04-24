<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AquWatch | Intelligent Water Monitoring</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Wave animation */
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
            height: 120px;
            animation: gentleWave 8s ease-in-out infinite alternate;
        }
        
        @keyframes gentleWave {
            0% { transform: translateX(0px) translateY(0px); }
            100% { transform: translateX(-15px) translateY(5px); }
        }
        
        /* Floating bubbles */
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
        
        /* Card hover effects */
        .login-card {
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 45px -15px rgba(0, 100, 120, 0.4);
        }
        
        /* Input focus effects */
        .input-focus-effect {
            transition: all 0.2s ease;
        }
        
        .input-focus-effect:focus {
            transform: scale(1.01);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3), 0 0 0 1px #0ea5e9;
        }
        
        /* Ripple button effect */
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
        
        /* Glow animation for logo */
        @keyframes softGlow {
            0%, 100% { filter: drop-shadow(0 4px 8px rgba(0,150,180,0.3)); }
            50% { filter: drop-shadow(0 8px 20px rgba(0,180,200,0.6)); }
        }
        
        .logo-glow {
            animation: softGlow 3s ease-in-out infinite;
        }
        
        /* Floating animation for decorative elements */
        @keyframes floatSoft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .float-icon {
            animation: floatSoft 3s ease-in-out infinite;
        }
        
        /* Custom scrollbar */
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
        
        /* Input autofill style override */
        input:-webkit-autofill,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px white inset;
            box-shadow: 0 0 0px 1000px white inset;
            -webkit-text-fill-color: #1e3a8a;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">

    <!-- === DYNAMIC BUBBLE CONTAINER === -->
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>
    
    <!-- Animated Wave Background -->
    <div class="wave-bg">
        <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow flex items-center justify-center px-4 py-10 relative z-10">
        <div class="w-full max-w-md">
            <!-- Login Card with Interactive Design -->
            <div class="login-card bg-white/85 backdrop-blur-md rounded-3xl shadow-2xl border border-white/60 p-8 transition-all duration-500">
                
                <!-- HEADER with Animated Logo -->
                <div class="text-center mb-8">
                    <div class="relative inline-block group">
                        <div class="absolute inset-0 bg-cyan-400/20 rounded-full blur-xl scale-110 group-hover:scale-125 transition-transform duration-700"></div>
                        <img src="{{ asset('images/logo.png') }}" 
                            alt="AquWatch Logo"
                            class="relative mx-auto mb-5 w-28 h-28 object-contain rounded-2xl shadow-2xl border-3 border-white/70 transition-all duration-500 group-hover:scale-105 logo-glow">
                    </div>
                    
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i class="fas fa-water text-cyan-600 text-2xl float-icon"></i>
                        <h2 class="text-3xl font-black bg-gradient-to-r from-blue-800 via-cyan-700 to-teal-700 bg-clip-text text-transparent">
                            Welcome Back
                        </h2>
                        <i class="fas fa-droplet text-teal-500 text-2xl float-icon" style="animation-delay: 0.5s"></i>
                    </div>
                    <p class="text-blue-700/80 text-sm">Sign in to monitor your water system</p>
                    <div class="h-1 w-16 bg-gradient-to-r from-cyan-400 to-blue-500 mx-auto rounded-full mt-3"></div>
                </div>

                <!-- Live Status Badge (Interactive) -->
                <div class="mb-6 flex justify-center">
                    <div class="inline-flex items-center gap-2 bg-cyan-100/70 backdrop-blur-sm rounded-full px-4 py-1.5 shadow-sm border border-cyan-300">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-cyan-800 tracking-wide">SECURE CONNECTION</span>
                        <i class="fas fa-shield-alt text-cyan-600 text-xs"></i>
                    </div>
                </div>

                <!-- SESSION STATUS -->
                @if (session('status'))
                    <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 text-center flex items-center justify-center gap-2 animate-pulse">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- LOGIN FORM -->
                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf

                    <!-- EMAIL FIELD -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-envelope text-cyan-600 text-xs"></i>
                            Email address
                        </label>
                        <div class="relative">
                            <input
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="hello@aquwatch.com"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <i class="fas fa-user-circle absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-300 text-lg"></i>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- PASSWORD FIELD -->
                    <div class="group">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-blue-800 flex items-center gap-2">
                                <i class="fas fa-lock text-cyan-600 text-xs"></i>
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs text-cyan-700 hover:text-cyan-900 transition-all duration-200 hover:underline flex items-center gap-1">
                                    <i class="fas fa-key text-xs"></i>
                                    Forgot?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input
                                id="password-field"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <button type="button" id="toggle-password" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-eye-slash text-lg"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- REMEMBER ME CHECKBOX -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                {{ old('remember') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-blue-300 text-cyan-600 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer transition-all"
                            >
                            <span class="text-sm text-blue-700 group-hover:text-blue-900 transition-colors">Remember me</span>
                        </label>
                        <div class="text-xs text-blue-500/70 flex items-center gap-1">
                            <i class="fas fa-shield-alt"></i>
                            <span>SSL Encrypted</span>
                        </div>
                    </div>

                    <!-- LOGIN BUTTON with ripple effect -->
                    <button
                        type="submit"
                        class="ripple-btn w-full rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold py-3.5 shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 text-base"
                        data-test="login-button"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        Log in to Dashboard
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <!-- DIVIDER -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-blue-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white/50 backdrop-blur-sm text-blue-600 rounded-full text-xs">Secure Access</span>
                    </div>
                </div>

                <!-- REGISTER LINK -->
                @if (Route::has('register'))
                    <div class="text-center">
                        <p class="text-sm text-blue-700">
                            New to AquWatch?
                            <a href="{{ route('register') }}" class="font-semibold text-cyan-700 hover:text-cyan-900 hover:underline transition-all duration-200 inline-flex items-center gap-1 group">
                                Create an account
                                <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </p>
                    </div>
                @endif

                <!-- Interactive Ocean Fact -->
                <div class="mt-6 pt-4 border-t border-blue-200/50 text-center">
                    <div class="flex items-center justify-center gap-2 text-xs text-blue-600/80">
                        <i class="fas fa-chart-line text-cyan-500"></i>
                        <span id="live-fact">🌊 Protecting 2.3M+ liters of water</span>
                        <i class="fas fa-heart text-rose-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER with interactive elements -->
    <footer class="relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 mt-auto border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-github"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fas fa-life-ring"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch — Intelligent Water Monitoring
        </p>
    </footer>

    <script>
        // ========== INTERACTIVE BUBBLES ==========
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
        
        // Generate bubbles periodically
        setInterval(createBubble, 400);
        for (let i = 0; i < 15; i++) setTimeout(createBubble, i * 200);
        
        // ========== PASSWORD TOGGLE VISIBILITY ==========
        const toggleBtn = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password-field');
        
        if (toggleBtn && passwordField) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }
        
        // ========== LIVE INTERACTIVE FACT ROTATOR ==========
        const facts = [
            '🌊 Protecting 2.3M+ liters of water',
            '💧 Real-time water quality monitoring',
            '🐟 Saving marine ecosystems worldwide',
            '📊 98.4% water purity index achieved',
            '⚡ 24/7 AI-powered anomaly detection',
            '🌱 15k+ trees preserved through conservation'
        ];
        let factIndex = 0;
        const factElement = document.getElementById('live-fact');
        
        if (factElement) {
            setInterval(() => {
                factIndex = (factIndex + 1) % facts.length;
                factElement.style.opacity = '0';
                setTimeout(() => {
                    factElement.innerHTML = facts[factIndex];
                    factElement.style.opacity = '1';
                }, 200);
            }, 4500);
        }
        
        // ========== RIPPLE EFFECT FOR BUTTON ==========
        const loginBtn = document.querySelector('.ripple-btn');
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                const rippleDiv = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size/2;
                const y = e.clientY - rect.top - size/2;
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
        }
        
        // ========== ANIMATED INPUT PLACEHOLDER EFFECT ==========
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement?.classList.add('ring-2', 'ring-cyan-300/50');
            });
            input.addEventListener('blur', function() {
                this.parentElement?.classList.remove('ring-2', 'ring-cyan-300/50');
            });
        });
        
        // ========== CARD MOUSE PARALLAX LIGHT EFFECT ==========
        const loginCard = document.querySelector('.login-card');
        if (loginCard) {
            loginCard.addEventListener('mousemove', (e) => {
                const rect = loginCard.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width;
                const y = (e.clientY - rect.top) / rect.height;
                const shadowX = (x - 0.5) * 15;
                const shadowY = (y - 0.5) * 15;
                loginCard.style.boxShadow = `${shadowX}px ${shadowY}px 35px rgba(0, 100, 130, 0.3)`;
            });
            loginCard.addEventListener('mouseleave', () => {
                loginCard.style.boxShadow = '';
            });
        }
        
        // ========== WELCOME MESSAGE TOAST (subtle) ==========
        setTimeout(() => {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-blue-800/85 backdrop-blur-md text-white px-5 py-2.5 rounded-full shadow-xl z-50 flex items-center gap-2 text-sm transition-all duration-500 pointer-events-none';
            toast.innerHTML = `<i class="fas fa-fish text-cyan-300"></i><span>Dive into clean water monitoring</span><i class="fas fa-water text-cyan-300"></i>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }, 1000);
        
        // ========== ADD TOOLTIPS TO ICONS ==========
        const helpIcons = document.querySelectorAll('.fa-question-circle, .fa-shield-alt');
        helpIcons.forEach(icon => {
            icon.setAttribute('title', 'Secure connection with 256-bit encryption');
        });
        
        console.log('🌊 AquWatch Login — Interactive Ocean Theme Active');
    </script>
</body>
</html>