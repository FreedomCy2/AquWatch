@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">FCM Tokens Diagnostics</h1>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Users & Their Tokens</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">User</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Token Count</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Tokens</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                            <td class="border border-gray-300 px-4 py-2 font-bold">{{ $user->fcmTokens->count() }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($user->fcmTokens->isNotEmpty())
                                    <ul class="text-sm space-y-1">
                                        @foreach ($user->fcmTokens as $token)
                                            <li class="text-gray-700 truncate" title="{{ $token->token }}">
                                                {{ substr($token->token, 0, 20) }}... ({{ $token->platform }})
                                                <br>
                                                <span class="text-xs text-gray-500">Updated: {{ $token->updated_at->diffForHumans() }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic">No tokens</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">All FCM Tokens (Recent First)</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">User</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Platform</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Token (First 30 chars)</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Last Used</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allTokens as $token)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">{{ $token->user?->name ?? '(null)' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $token->platform }}</td>
                            <td class="border border-gray-300 px-4 py-2 font-mono text-sm">{{ substr($token->token, 0, 30) }}...</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">{{ $token->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">No tokens found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 p-4 rounded">
        <h3 class="font-semibold text-blue-900">What to check:</h3>
        <ul class="text-sm text-blue-800 mt-2 space-y-1">
            <li>✓ Does your friend's name appear in the left table with at least 1 token?</li>
            <li>✓ If not, their browser did not register the token (permission/SW issue).</li>
            <li>✓ If yes, verify your token is also there when you send notifications.</li>
            <li>✓ Check browser console on friend's phone for errors during token registration.</li>
        </ul>
    </div>
</div>
@endsection
