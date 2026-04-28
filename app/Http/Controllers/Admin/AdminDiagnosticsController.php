<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\View\View;

class AdminDiagnosticsController extends Controller
{
    public function fcmTokens(): View
    {
        $users = User::query()
            ->with('fcmTokens')
            ->orderBy('name')
            ->get();

        $allTokens = FcmToken::query()
            ->with('user')
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.diagnostics.fcm-tokens', [
            'users' => $users,
            'allTokens' => $allTokens,
        ]);
    }
}
