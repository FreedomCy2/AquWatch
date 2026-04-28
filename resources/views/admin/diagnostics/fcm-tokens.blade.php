<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCM Tokens Diagnostics - AquWatch Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 text-slate-800">
    <header class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-3xl font-black text-blue-900">FCM Tokens Diagnostics</h1>
            <p class="text-blue-800/80 text-sm">Inspect whether each user has a saved FCM token on the VPS</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-xl bg-white/70 border border-white font-semibold text-blue-900">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 pb-10 space-y-6">
        @if (! $tableExists)
            <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-amber-900 shadow-sm">
                The <strong>fcm_tokens</strong> table is missing on this database. Run <code>php artisan migrate --force</code> on the VPS first, then reload this page.
            </div>
        @endif

        <section class="bg-white/70 rounded-2xl border border-white shadow p-5 md:p-6">
            <h2 class="text-xl font-bold text-blue-900 mb-4">Users & Their Tokens</h2>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] border-collapse border border-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-200 px-4 py-2 text-left">User</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Email</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Token Count</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Tokens</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/80">
                                <td class="border border-slate-200 px-4 py-2">{{ $user->name }}</td>
                                <td class="border border-slate-200 px-4 py-2">{{ $user->email }}</td>
                                <td class="border border-slate-200 px-4 py-2 font-bold">{{ $user->fcmTokens->count() }}</td>
                                <td class="border border-slate-200 px-4 py-2">
                                    @if ($user->fcmTokens->isNotEmpty())
                                        <ul class="text-sm space-y-1">
                                            @foreach ($user->fcmTokens as $token)
                                                <li class="text-slate-700 break-all" title="{{ $token->token }}">
                                                    {{ substr($token->token, 0, 20) }}... ({{ $token->platform }})
                                                    <br>
                                                    <span class="text-xs text-slate-500">Updated: {{ $token->updated_at->diffForHumans() }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-slate-400 italic">No tokens</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border border-slate-200 px-4 py-3 text-center text-slate-500">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white/70 rounded-2xl border border-white shadow p-5 md:p-6">
            <h2 class="text-xl font-bold text-blue-900 mb-4">All FCM Tokens (Recent First)</h2>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] border-collapse border border-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-200 px-4 py-2 text-left">User</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Platform</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Token</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Last Used</th>
                            <th class="border border-slate-200 px-4 py-2 text-left">Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allTokens as $token)
                            <tr class="hover:bg-slate-50/80">
                                <td class="border border-slate-200 px-4 py-2">{{ $token->user?->name ?? '(null)' }}</td>
                                <td class="border border-slate-200 px-4 py-2">{{ $token->platform }}</td>
                                <td class="border border-slate-200 px-4 py-2 font-mono text-xs break-all">{{ substr($token->token, 0, 60) }}...</td>
                                <td class="border border-slate-200 px-4 py-2 text-sm">{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="border border-slate-200 px-4 py-2 text-sm">{{ $token->updated_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-slate-200 px-4 py-3 text-center text-slate-500">No tokens found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-sm">
            <h3 class="font-semibold text-blue-900">What to check:</h3>
            <ul class="text-sm text-blue-800 mt-2 space-y-1">
                <li>✓ Does your friend's name appear in the left table with at least 1 token?</li>
                <li>✓ If not, their app/browser did not register the token after permission was allowed.</li>
                <li>✓ If yes, compare whether your own token is also present before sending.</li>
                <li>✓ If the page 500s again, the VPS log will show the exact database or PHP error.</li>
            </ul>
        </section>
    </main>
</body>
</html>
