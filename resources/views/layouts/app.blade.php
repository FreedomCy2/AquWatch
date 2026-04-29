<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}

        @auth
        <script>
        (function () {
            try {
                if (window.AndroidBridge
                    && typeof window.AndroidBridge.isApp === 'function'
                    && window.AndroidBridge.isApp()) {
                    var token = window.AndroidBridge.getFcmToken();
                    if (token) {
                        fetch('/api/save-fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                token: token,
                                platform: 'android',
                                user_id: {{ auth()->id() }}
                            })
                        }).catch(function (e) { console.warn('FCM link failed', e); });
                    }
                }
            } catch (e) { console.warn(e); }
        })();
        </script>
        @endauth
    </flux:main>
</x-layouts::app.sidebar>
