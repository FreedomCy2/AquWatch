<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - AquWatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }

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
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">

    <!-- Bubble Container -->
    <div id="bubble-container" class="fixed inset-0 pointer-events-none z-0"></div>

    <!-- Wave Background -->
    <div class="wave-bg">
        <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L0,320Z"></path>
            <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L0,320Z"></path>
            <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z"></path>
        </svg>
    </div>

    <main class="flex-grow relative z-10">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">Edit Profile</h1>
                    <p class="text-blue-800/80 mt-1">Update your account information.</p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile.show') }}"
                       class="px-5 py-2.5 bg-white/80 border border-white rounded-xl shadow text-blue-900 hover:bg-white transition">
                        Back to Profile
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 md:p-8">
                <form method="POST" action="{{ route('profile.legacy.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Timezone</label>
                            <select name="timezone"
                                    class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                @php $selectedTimezone = old('timezone', $user->timezone ?: 'Asia/Brunei'); @endphp
                                @foreach($timezones as $timezone)
                                    <option value="{{ $timezone }}" {{ $selectedTimezone === $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Profile Photo</label>
                            <input type="file" name="photo"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Role</label>
                            <select name="role"
                                    class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                <option value="">Select role</option>
                                @php $selectedRole = old('role', $profile->role); @endphp
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ $selectedRole === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Preferred Language</label>
                            <select name="preferred_language"
                                    class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                @php $selectedLanguage = old('preferred_language', $profile->preferred_language ?: 'en'); @endphp
                                <option value="en" {{ $selectedLanguage === 'en' ? 'selected' : '' }}>English</option>
                                <option value="ms" {{ $selectedLanguage === 'ms' ? 'selected' : '' }}>Bahasa Melayu</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Birth Date</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', optional($profile->birth_date)->format('Y-m-d')) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Company</label>
                            <input type="text" name="company" value="{{ old('company', $profile->company) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Job Title</label>
                            <input type="text" name="job_title" value="{{ old('job_title', $profile->job_title) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Address</label>
                            <input type="text" name="address" value="{{ old('address', $profile->address) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">District</label>
                            <select name="district"
                                    class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                                <option value="">Select district</option>
                                @php $selectedDistrict = old('district', $profile->district); @endphp
                                @foreach($districts as $district)
                                    <option value="{{ $district }}" {{ $selectedDistrict === $district ? 'selected' : '' }}>{{ $district }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Mukim (Optional)</label>
                            <input type="text" name="mukim" value="{{ old('mukim', $profile->mukim) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Latitude (Optional)</label>
                            <input type="number" step="0.000001" name="latitude" value="{{ old('latitude', $profile->latitude) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Longitude (Optional)</label>
                            <input type="number" step="0.000001" name="longitude" value="{{ old('longitude', $profile->longitude) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Country</label>
                            <input type="text" name="country" value="{{ old('country', $profile->country ?: 'Brunei Darussalam') }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-slate-50 text-slate-600 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Website</label>
                            <input type="text" name="website" value="{{ old('website', $profile->website) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Twitter</label>
                            <input type="text" name="twitter" value="{{ old('twitter', $profile->twitter) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">LinkedIn</label>
                            <input type="text" name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">GitHub</label>
                            <input type="text" name="github" value="{{ old('github', $profile->github) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Bio</label>
                            <textarea name="bio" rows="5"
                                      class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">{{ old('bio', $profile->bio) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold shadow-lg hover:scale-[1.02] transition">
                            Save Changes
                        </button>
                    </div>
                </form>
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
            © {{ date('Y') }} AquWatch — Protecting our waters with real-time intelligence
        </p>
    </footer>

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

            setTimeout(() => {
                bubble.remove();
            }, 10000);
        }

        setInterval(createBubble, 400);
    </script>
</body>
</html>