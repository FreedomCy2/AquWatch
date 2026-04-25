<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AquWatch | Join Ocean Protection</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
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
        .register-card {
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .register-card:hover {
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
        
        /* Password strength indicator */
        .strength-bar {
            transition: all 0.3s ease;
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
        
        /* Success animation */
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
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
            <!-- Register Card with Interactive Design -->
            <div class="register-card bg-white/85 backdrop-blur-md rounded-3xl shadow-2xl border border-white/60 p-8 transition-all duration-500">
                
                <!-- HEADER with Animated Logo -->
                <div class="text-center mb-6">
                    <div class="relative inline-block group">
                        <div class="absolute inset-0 bg-cyan-400/20 rounded-full blur-xl scale-110 group-hover:scale-125 transition-transform duration-700"></div>
                        <img src="{{ asset('images/logo.png') }}" 
                            alt="AquWatch Logo"
                            class="relative mx-auto mb-4 w-28 h-28 object-contain rounded-2xl shadow-2xl border-3 border-white/70 transition-all duration-500 group-hover:scale-105 logo-glow">
                    </div>
                    
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <h2 class="text-3xl font-black bg-gradient-to-r from-blue-800 via-cyan-700 to-teal-700 bg-clip-text text-transparent">
                            Join the Wave
                        </h2>
                        <i class="fas fa-water text-teal-500 text-2xl float-icon" style="animation-delay: 0.5s"></i>
                    </div>
                    <p class="text-blue-700/80 text-sm">Start to easily monitor your water</p>
                    <div class="h-1 w-20 bg-gradient-to-r from-cyan-400 to-blue-500 mx-auto rounded-full mt-3"></div>
                </div>


                <!-- SESSION STATUS -->
                @if (session('status'))
                    <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 text-center flex items-center justify-center gap-2 animate-pulse">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- REGISTRATION FORM -->
                <form method="POST" action="{{ route('register.store') }}" class="space-y-4" id="register-form">
                    @csrf

                    <!-- NAME FIELD -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-user text-cyan-600 text-xs"></i>
                            Full Name
                        </label>
                        <div class="relative">
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Alex Johnson"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <i class="fas fa-user-circle absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-300 text-lg"></i>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- EMAIL FIELD -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-envelope text-cyan-600 text-xs"></i>
                            Email Address
                        </label>
                        <div class="relative">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="hello@aquwatch.com"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <i class="fas fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-300 text-lg"></i>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-user-shield text-cyan-600 text-xs"></i>
                            Account Type
                        </label>
                        <div class="relative">
                            <select
                                id="role"
                                name="role"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                                required
                            >
                                <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div id="admin-code-wrapper" class="group {{ old('role') === 'admin' ? '' : 'hidden' }}">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-key text-cyan-600 text-xs"></i>
                            Admin Code
                        </label>
                        <div class="relative">
                            <input
                                id="admin_code"
                                name="admin_code"
                                type="password"
                                value="{{ old('admin_code') }}"
                                autocomplete="off"
                                placeholder="Enter admin code"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <i class="fas fa-key absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-300 text-lg"></i>
                        </div>
                        @error('admin_code')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- PASSWORD FIELD with strength meter -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-lock text-cyan-600 text-xs"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Create a strong password"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <button type="button" id="toggle-password" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-eye-slash text-lg"></i>
                            </button>
                        </div>
                        
                        <!-- Password Strength Indicator -->

                        
                        @error('password')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- CONFIRM PASSWORD FIELD -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center gap-2">
                            <i class="fas fa-check-circle text-cyan-600 text-xs"></i>
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm your password"
                                class="input-focus-effect w-full rounded-xl border border-blue-200 bg-white/90 px-4 py-3.5 text-blue-900 placeholder-blue-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-200"
                            >
                            <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-300 text-lg"></i>
                        </div>
                        <div id="password-match-feedback" class="mt-1 text-xs hidden"></div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="flex items-start gap-2 pt-2">
                        <input
                            type="checkbox"
                            id="terms"
                            name="terms"
                            required
                            class="mt-1 w-4 h-4 rounded border-blue-300 text-cyan-600 focus:ring-cyan-400 focus:ring-offset-0 cursor-pointer"
                        >
                        <label for="terms" class="text-xs text-blue-700 leading-tight">
                            I agree to the <a href="#" class="text-cyan-700 hover:underline font-medium">Terms of Service</a> and 
                            <a href="#" class="text-cyan-700 hover:underline font-medium">Privacy Policy</a>, and I'm excited to protect our oceans! 🌊
                        </label>
                    </div>

                    <!-- REGISTER BUTTON with ripple effect -->
                    <button
                        type="submit"
                        id="register-btn"
                        class="ripple-btn w-full rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold py-3.5 shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3 text-base mt-4"
                        data-test="register-user-button"
                    >
                        <i class="fas fa-user-plus"></i>
                        Create Account
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <!-- DIVIDER -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-blue-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white/50 backdrop-blur-sm text-blue-600 rounded-full text-xs flex items-center gap-1">
                            <i class="fas fa-shield-alt"></i> 100% Secure
                        </span>
                    </div>
                </div>

                <!-- LOGIN LINK -->
                <div class="text-center">
                    <p class="text-sm text-blue-700">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-cyan-700 hover:text-cyan-900 hover:underline transition-all duration-200 inline-flex items-center gap-1 group">
                            Log in here
                            <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </p>
                </div>



    <!-- FOOTER with interactive elements -->
    <footer class="relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 mt-auto border-t border-white/40">
        <div class="flex justify-center gap-6 mb-2">
            <a href="https://x.com/AquWatch" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/aquwatch/" target="_blank" rel="noopener noreferrer" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fab fa-instagram"></i></a>
            <a href="#" class="hover:text-cyan-800 transition-all duration-200 transform hover:scale-110 inline-block"><i class="fas fa-heart"></i></a>
        </div>
        <p class="text-xs">
            <i class="fas fa-water mr-1"></i>
            © {{ date('Y') }} AquWatch — Every drop counts. Join the movement.
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
        
        setInterval(createBubble, 400);
        for (let i = 0; i < 15; i++) setTimeout(createBubble, i * 200);
        
        // ========== PASSWORD TOGGLE VISIBILITY ==========
        const toggleBtn = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password');
        
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

        const roleSelect = document.getElementById('role');
        const adminCodeWrapper = document.getElementById('admin-code-wrapper');
        const adminCodeInput = document.getElementById('admin_code');

        function syncAdminCodeVisibility() {
            const isAdmin = roleSelect && roleSelect.value === 'admin';

            if (!adminCodeWrapper || !adminCodeInput) {
                return;
            }

            adminCodeWrapper.classList.toggle('hidden', !isAdmin);
            adminCodeInput.required = Boolean(isAdmin);

            if (!isAdmin) {
                adminCodeInput.value = '';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', syncAdminCodeVisibility);
            syncAdminCodeVisibility();
        }
        
 

        
        // ========== RIPPLE EFFECT FOR BUTTON ==========
        const registerBtn = document.getElementById('register-btn');
        if (registerBtn) {
            registerBtn.addEventListener('click', function(e) {
                const termsCheckbox = document.getElementById('terms');
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-24 left-1/2 transform -translate-x-1/2 bg-amber-500/90 backdrop-blur-md text-white px-5 py-3 rounded-xl shadow-xl z-50 flex items-center gap-2 text-sm transition-all duration-500';
                    toast.innerHTML = `<i class="fas fa-exclamation-triangle"></i><span>Please accept the Terms to continue</span>`;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 500);
                    }, 2500);
                    return;
                }
                
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
        
        // ========== ANIMATED INPUT FOCUS EFFECTS ==========
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement?.classList.add('ring-2', 'ring-cyan-300/50');
            });
            input.addEventListener('blur', function() {
                this.parentElement?.classList.remove('ring-2', 'ring-cyan-300/50');
            });
        });
        
        // ========== CARD MOUSE PARALLAX LIGHT EFFECT ==========
        const registerCard = document.querySelector('.register-card');
        if (registerCard) {
            registerCard.addEventListener('mousemove', (e) => {
                const rect = registerCard.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width;
                const y = (e.clientY - rect.top) / rect.height;
                const shadowX = (x - 0.5) * 15;
                const shadowY = (y - 0.5) * 15;
                registerCard.style.boxShadow = `${shadowX}px ${shadowY}px 35px rgba(0, 100, 130, 0.3)`;
            });
            registerCard.addEventListener('mouseleave', () => {
                registerCard.style.boxShadow = '';
            });
        }
        
        console.log('🌊 AquWatch Registration — Interactive Ocean Theme Active');
    </script>
</body>
</html>