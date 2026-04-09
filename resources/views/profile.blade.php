<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AquaWatch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200">

    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">My Profile</h1>
                <p class="text-blue-800/80 mt-1">Manage your personal information here.</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 bg-white/80 border border-white rounded-xl shadow text-blue-900 hover:bg-white transition">
                    Back to Dashboard
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

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Card -->
            <div class="bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 h-fit">
                <div class="text-center">
                    <div class="w-28 h-28 mx-auto rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center text-4xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h2 class="mt-4 text-2xl font-bold text-blue-900">{{ $user->name }}</h2>
                    <p class="text-blue-700">{{ $user->email }}</p>

                    <div class="mt-6 text-left space-y-3">
                        <div class="bg-sky-50 rounded-xl px-4 py-3">
                            <p class="text-xs text-blue-600 font-semibold">Phone</p>
                            <p class="text-blue-900">{{ $profile->phone ?? 'Not set' }}</p>
                        </div>

                        <div class="bg-sky-50 rounded-xl px-4 py-3">
                            <p class="text-xs text-blue-600 font-semibold">Company</p>
                            <p class="text-blue-900">{{ $profile->company ?? 'Not set' }}</p>
                        </div>

                        <div class="bg-sky-50 rounded-xl px-4 py-3">
                            <p class="text-xs text-blue-600 font-semibold">Job Title</p>
                            <p class="text-blue-900">{{ $profile->job_title ?? 'Not set' }}</p>
                        </div>

                        <div class="bg-sky-50 rounded-xl px-4 py-3">
                            <p class="text-xs text-blue-600 font-semibold">Location</p>
                            <p class="text-blue-900">
                                {{ ($profile->city ?? '') . (($profile->city && $profile->country) ? ', ' : '') . ($profile->country ?? '') ?: 'Not set' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div class="lg:col-span-2 bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 md:p-8">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf

                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-blue-900">Edit Profile</h3>
                        <p class="text-blue-700">Update your account details below.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
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
                            <label class="block mb-2 text-sm font-semibold text-blue-900">City</label>
                            <input type="text" name="city" value="{{ old('city', $profile->city) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Country</label>
                            <input type="text" name="country" value="{{ old('country', $profile->country) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Website</label>
                            <input type="text" name="website" value="{{ old('website', $profile->website) }}"
                                   class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-blue-900">Avatar URL</label>
                            <input type="text" name="avatar" value="{{ old('avatar', $profile->avatar) }}"
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

                        <div class="md:col-span-2">
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
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>