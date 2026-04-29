<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.settings') }} - AquWatch</title>
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
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 relative overflow-x-hidden">
<div class="wave-bg">
    <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
        <path fill="#b3e5fc" fill-opacity="0.5" d="M0,192L48,197.3C96,203,192,213,288,208C384,203,480,181,576,181.3C672,181,768,203,864,213.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L0,320Z"></path>
        <path fill="#81d4fa" fill-opacity="0.6" d="M0,224L48,213.3C96,203,192,181,288,176C384,171,480,181,576,197.3C672,213,768,235,864,229.3C960,224,1056,192,1152,176C1248,160,1344,160,1392,160L1440,160L1440,320L0,320Z"></path>
        <path fill="#4fc3f7" fill-opacity="0.7" d="M0,256L48,250.7C96,245,192,235,288,234.7C384,235,480,245,576,250.7C672,256,768,256,864,245.3C960,235,1056,213,1152,202.7C1248,192,1344,192,1392,192L1440,192L1440,320L0,320Z"></path>
    </svg>
</div>

<main class="flex-grow relative z-10">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">{{ __('ui.settings_title') }}</h1>
                <p class="text-blue-800/80 mt-1">{{ __('ui.settings_subtitle') }}</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('profile.show') }}"
                   class="px-5 py-2.5 bg-white/80 border border-white rounded-xl shadow text-blue-900 hover:bg-white transition">
                    {{ __('ui.profile') }}
                </a>
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 bg-white/80 border border-white rounded-xl shadow text-blue-900 hover:bg-white transition">
                    {{ __('ui.dashboard') }}
                </a>
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

        <div class="bg-white/70 backdrop-blur-md rounded-3xl shadow-xl border border-white/60 p-6 md:p-8">
            <form method="POST" action="{{ route('account.settings.update') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.timezone') }}</label>
                        <select name="timezone" class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            @php $selectedTimezone = old('timezone', $settings['timezone']); @endphp
                            @foreach($timezones as $timezone)
                                <option value="{{ $timezone }}" {{ $selectedTimezone === $timezone ? 'selected' : '' }}>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.language') }}</label>
                        <select name="preferred_language" class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            @php $selectedLanguage = old('preferred_language', $settings['preferred_language']); @endphp
                            @foreach($languages as $language)
                                <option value="{{ $language }}" {{ $selectedLanguage === $language ? 'selected' : '' }}>
                                    {{ $language === 'ms' ? __('ui.malay') : __('ui.english') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h2 class="text-lg font-bold text-blue-900 mb-3">{{ __('ui.alert_thresholds') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.flood_trigger_level') }}</label>
                        <select name="flood_trigger" class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            @foreach($floodTriggers as $trigger)
                                <option value="{{ $trigger }}" {{ $settings['flood_trigger'] === $trigger ? 'selected' : '' }}>{{ $trigger }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.rain_trigger_level') }}</label>
                        <select name="rain_trigger" class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            @foreach($rainTriggers as $trigger)
                                <option value="{{ $trigger }}" {{ $settings['rain_trigger'] === $trigger ? 'selected' : '' }}>{{ $trigger === 'heavy_rain' ? 'heavy_rain' : 'rain' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.flow_anomaly_threshold') }}</label>
                        <input type="number" name="flow_anomaly_percent" min="5" max="200"
                               value="{{ $settings['flow_anomaly_percent'] }}"
                               class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.data_retention') }}</label>
                        <select name="retention_days" class="w-full rounded-xl border border-blue-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            @foreach([7, 30, 90, 365] as $days)
                                <option value="{{ $days }}" {{ (int) $settings['retention_days'] === $days ? 'selected' : '' }}>{{ $days }} {{ __('ui.days') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h2 class="text-lg font-bold text-blue-900 mb-3">{{ __('ui.quiet_escalation') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.quiet_start') }}</label>
                        <input type="time" name="quiet_hours_start" value="{{ $settings['quiet_hours_start'] }}"
                               class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.quiet_end') }}</label>
                        <input type="time" name="quiet_hours_end" value="{{ $settings['quiet_hours_end'] }}"
                               class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold text-blue-900">{{ __('ui.escalation_contact') }}</label>
                        <input type="text" name="escalation_contact" value="{{ $settings['escalation_contact'] }}"
                               placeholder="{{ __('ui.contact_placeholder') }}"
                               class="w-full rounded-xl border border-blue-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    </div>
                </div>

                <div class="mt-7 rounded-2xl border border-blue-200 bg-blue-50/70 p-4 md:p-5">
                    <h2 class="text-lg font-bold text-blue-900 mb-1">{{ __('ui.push_notifications') }}</h2>
                    <p class="text-sm text-blue-800/90 mb-4">{{ __('ui.push_notifications_help') }}</p>

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            id="resend-notification-permission"
                            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold shadow hover:scale-[1.02] transition"
                        >
                            {{ __('ui.enable_notifications_again') }}
                        </button>
                        <span id="notification-permission-status" class="text-sm text-blue-900/90"></span>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold shadow-lg hover:scale-[1.02] transition">
                        {{ __('ui.save_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<footer class="relative z-10 text-center text-blue-800/80 py-5 text-sm backdrop-blur-sm bg-white/20 mt-8 border-t border-white/40">
    <p class="text-xs">
        <i class="fas fa-water mr-1"></i>
        © {{ date('Y') }} AquWatch — Protecting our waters with real-time intelligence
    </p>
</footer>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.3/firebase-messaging-compat.js"></script>
<script>
    const resendPermissionButton = document.getElementById('resend-notification-permission');
    const permissionStatusEl = document.getElementById('notification-permission-status');

    const fcmTokenSaveUrl = @json(route('fcm-token.store'));
    const csrfToken = @json(csrf_token());
    const firebaseConfig = {
        apiKey: @json(config('services.firebase.web_api_key')),
        authDomain: @json(config('services.firebase.web_auth_domain')),
        projectId: @json(config('services.firebase.web_project_id')),
        storageBucket: @json(config('services.firebase.web_storage_bucket')),
        messagingSenderId: @json(config('services.firebase.web_messaging_sender_id')),
        appId: @json(config('services.firebase.web_app_id')),
        measurementId: @json(config('services.firebase.web_measurement_id')),
    };
    const firebaseVapidKey = @json(config('services.firebase.web_vapid_key'));

    const i18nPermissionGranted = @json(__('ui.permission_granted_token_saved'));
    const i18nPermissionDenied = @json(__('ui.permission_denied_browser_settings'));
    const i18nPermissionDismissed = @json(__('ui.permission_not_granted'));
    const i18nPermissionUnsupported = @json(__('ui.permission_unsupported'));
    const i18nPermissionProcessing = @json(__('ui.permission_requesting'));
    const i18nPermissionSaveFailed = @json(__('ui.permission_save_failed'));
    const i18nMissingFirebaseConfig = @json(__('ui.permission_missing_firebase'));

    function hasFirebaseWebConfig(config) {
        return Boolean(
            config.apiKey &&
            config.authDomain &&
            config.projectId &&
            config.storageBucket &&
            config.messagingSenderId &&
            config.appId
        );
    }

    async function saveFcmToken(token) {
        const response = await fetch(fcmTokenSaveUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                token,
                platform: 'web',
            }),
        });

        if (!response.ok) {
            throw new Error('Failed to save FCM token.');
        }

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw new Error('Unexpected response while saving FCM token.');
        }

        const payload = await response.json();
        if (!payload?.id) {
            throw new Error('Missing token id in response.');
        }

        return payload;
    }

    function setPermissionStatus(message, tone = 'default') {
        permissionStatusEl.textContent = message;
        permissionStatusEl.className = 'text-sm';

        if (tone === 'success') {
            permissionStatusEl.classList.add('text-emerald-700', 'font-medium');
            return;
        }

        if (tone === 'error') {
            permissionStatusEl.classList.add('text-red-700', 'font-medium');
            return;
        }

        permissionStatusEl.classList.add('text-blue-900', 'opacity-90');
    }

    async function resendNotificationPermission() {
        if (!window.isSecureContext || !('Notification' in window) || !('serviceWorker' in navigator)) {
            setPermissionStatus(i18nPermissionUnsupported, 'error');
            return;
        }

        if (!hasFirebaseWebConfig(firebaseConfig) || !firebaseVapidKey || !window.firebase?.apps) {
            setPermissionStatus(i18nMissingFirebaseConfig, 'error');
            return;
        }

        if (Notification.permission === 'denied') {
            setPermissionStatus(i18nPermissionDenied, 'error');
            return;
        }

        resendPermissionButton.disabled = true;
        setPermissionStatus(i18nPermissionProcessing);

        try {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            const permission = Notification.permission === 'default'
                ? await Notification.requestPermission()
                : Notification.permission;

            if (permission !== 'granted') {
                setPermissionStatus(i18nPermissionDismissed, 'error');
                return;
            }

            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            const messaging = firebase.messaging();
            const token = await messaging.getToken({
                vapidKey: firebaseVapidKey,
                serviceWorkerRegistration: registration,
            });

            if (!token) {
                setPermissionStatus(i18nPermissionSaveFailed, 'error');
                return;
            }

            const saved = await saveFcmToken(token);
            setPermissionStatus(`${i18nPermissionGranted} (${String(token).slice(-8)})`, 'success');
            window.dispatchEvent(new CustomEvent('fcm-token-updated', {
                detail: {
                    token,
                    id: saved.id,
                },
            }));
        } catch (error) {
            console.warn('Manual FCM permission/token setup failed:', error);
            setPermissionStatus(i18nPermissionSaveFailed, 'error');
        } finally {
            resendPermissionButton.disabled = false;
        }
    }

    resendPermissionButton?.addEventListener('click', resendNotificationPermission);
</script>
</body>
</html>
