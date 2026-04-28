<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDiagnosticsController extends Controller
{
    public function fcmTokens(): View
    {
        $tableExists = Schema::hasTable('fcm_tokens');

        $users = User::query()
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($tableExists) {
                if (! $tableExists) {
                    $user->setRelation('fcmTokens', collect());

                    return $user;
                }

                $user->load('fcmTokens');

                return $user;
            });

        $allTokens = $tableExists
            ? FcmToken::query()
                ->with('user')
                ->orderByDesc('updated_at')
                ->get()
            : collect();

        return view('admin.diagnostics.fcm-tokens', [
            'users' => $users,
            'allTokens' => $allTokens,
            'tableExists' => $tableExists,
        ]);
    }
}
