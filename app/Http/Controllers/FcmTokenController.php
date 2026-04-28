<?php
namespace App\Http\Controllers;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller {
    public function store(Request $request) {
        $data = $request->validate([
            'token' => 'required|string|max:512',
            'platform' => 'nullable|string|max:20',
        ]);
        FcmToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => $request->user()?->id,
                'platform' => $data['platform'] ?? 'web',
                'last_used_at' => now(),
            ]
        );
        return response()->json(['ok' => true]);
    }
}
