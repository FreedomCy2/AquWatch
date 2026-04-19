<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AquWatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">

<div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

<div class="wave-bg">
    <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
        <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L0,320Z"></path>
        <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L0,320Z"></path>
        <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z"></path>
    </svg>
</div>

<main class="flex-grow">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">My Profile</h1>
                <p class="text-blue-800/80 mt-1">View your account details here.</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 bg-white/80 border border-white rounded-xl shadow text-blue-900 hover:bg-white transition">
                    Back to Dashboard
                </a>

                <a href="{{ route('profile.legacy.edit') }}"
                   class="px-5 py-2.5 bg-cyan-600 text-white rounded-xl shadow hover:bg-cyan-700 transition">
                    Edit Profile
                </a>

                <a href="{{ route('account.settings.edit') }}"
                   class="px-5 py-2.5 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition">
                    Settings
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 bg-red-500 text-white rounded-xl shadow hover:bg-red-600 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 h-fit">
                <div class="text-center">
                    @if($profile->photo)
                        <img src="{{ asset('storage/' . $profile->photo) }}"
                             alt="Profile Photo"
                             class="w-28 h-28 mx-auto rounded-full object-cover shadow-lg border-4 border-white">
                    @else
                        <div class="w-28 h-28 mx-auto rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center text-4xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <h2 class="mt-4 text-2xl font-bold text-blue-900">{{ $user->name }}</h2>
                    <p class="text-blue-700">{{ $user->email }}</p>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 md:p-8">
                <h3 class="text-2xl font-bold text-blue-900 mb-6">Account Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Name</p>
                        <p class="text-blue-900">{{ $user->name }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Email</p>
                        <p class="text-blue-900">{{ $user->email }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Timezone</p>
                        <p class="text-blue-900">{{ $user->timezone ?: 'Asia/Brunei' }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Phone</p>
                        <p class="text-blue-900">{{ $profile->phone ?? 'Not set' }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Role</p>
                        <p class="text-blue-900">{{ $profile->role ? ucfirst($profile->role) : 'Not set' }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Preferred Language</p>
                        <p class="text-blue-900">{{ $profile->preferred_language === 'ms' ? 'Bahasa Melayu' : ($profile->preferred_language === 'en' ? 'English' : 'Not set') }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Company</p>
                        <p class="text-blue-900">{{ $profile->company ?? 'Not set' }}</p>
                    </div>

                    <div class="bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Job Title</p>
                        <p class="text-blue-900">{{ $profile->job_title ?? 'Not set' }}</p>
                    </div>

                    <div class="md:col-span-2 bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Location</p>
                        <p class="text-blue-900">
                            {{ ($profile->district ?? '') . (($profile->district && $profile->mukim) ? ', ' : '') . ($profile->mukim ?? '') . (($profile->district || $profile->mukim) ? ', ' : '') . ($profile->country ?? 'Brunei Darussalam') }}
                        </p>
                    </div>

                    <div class="md:col-span-2 bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Coordinates</p>
                        <p class="text-blue-900">{{ $profile->latitude && $profile->longitude ? $profile->latitude . ', ' . $profile->longitude : 'Not set' }}</p>
                    </div>

                    <div class="md:col-span-2 bg-sky-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-blue-600 font-semibold">Bio</p>
                        <p class="text-blue-900">{{ $profile->bio ?? 'Not set' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
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

    container.appendChild(bubble);
    setTimeout(() => bubble.remove(), 10000);
}

setInterval(createBubble, 400);
</script>    
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
</body>
</html>