<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlowDisplayController;
use App\Http\Controllers\FloodDisplayController;
use App\Http\Controllers\GraphDisplayController;
use App\Http\Controllers\RainDisplayController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\RainGraphDisplayController;
use App\Http\Controllers\AiInsightController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AlertNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserAnnouncementController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDiagnosticsController;
use App\Models\Sensor;

Route::get('/', function () {
        $totalSensors = Sensor::query()->where('is_active', true)->count();
        $onlineSensors = Sensor::query()
                ->where('is_active', true)
        ->where('last_seen_at', '>=', now()->subSeconds(30))
                ->count();

        $allSensorsOnline = $totalSensors > 0 && $onlineSensors === $totalSensors;

        return view('welcome', [
                'totalSensors' => $totalSensors,
                'onlineSensors' => $onlineSensors,
                'allSensorsOnline' => $allSensorsOnline,
        ]);
})->name('home');

        Route::get('/sensor-status', function () {
            $totalSensors = Sensor::query()->where('is_active', true)->count();
            $onlineSensors = Sensor::query()
                ->where('is_active', true)
                ->where('last_seen_at', '>=', now()->subSeconds(30))
                ->count();

            return response()->json([
                'totalSensors' => $totalSensors,
                'onlineSensors' => $onlineSensors,
                'allSensorsOnline' => $totalSensors > 0 && $onlineSensors === $totalSensors,
            ]);
        })->name('sensor-status.data');

Route::get('/plans', [PlanController::class, 'index'])->name('plans');

Route::get('/firebase-messaging-sw-config.js', function () {
    return response()->stream(function (): void {
        echo 'self.FIREBASE_CONFIG = '.json_encode([
            'apiKey' => config('services.firebase.web_api_key'),
            'authDomain' => config('services.firebase.web_auth_domain'),
            'projectId' => config('services.firebase.web_project_id'),
            'storageBucket' => config('services.firebase.web_storage_bucket'),
            'messagingSenderId' => config('services.firebase.web_messaging_sender_id'),
            'appId' => config('services.firebase.web_app_id'),
            'measurementId' => config('services.firebase.web_measurement_id'),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).';';
    }, 200, [
        'Content-Type' => 'application/javascript; charset=UTF-8',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
    ]);
})->name('firebase-messaging-sw.config');

Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/plans/switch', [PlanController::class, 'switchPlan'])->name('plans.switch');

        Route::post('/fcm/token', [FcmTokenController::class, 'store'])->name('fcm-token.store');
        Route::delete('/fcm/token', [FcmTokenController::class, 'destroy'])->name('fcm-token.destroy');
        
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.legacy.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.legacy.update');
    Route::get('/account/settings', [SettingsController::class, 'edit'])->name('account.settings.edit');
    Route::post('/account/settings', [SettingsController::class, 'update'])->name('account.settings.update');

    Route::redirect('/rain-display', '/contents/rain-display');
    Route::redirect('/flood-display', '/contents/flood-display');
    Route::redirect('/flow-display', '/contents/flow-display');
    Route::redirect('/graph-display', '/contents/graph-display');
    Route::redirect('/notifications', '/contents/notifications');
    Route::redirect('/announcements', '/contents/announcements');

    Route::get('contents/rain-display', [RainDisplayController::class, 'index'])
        ->name('contents.rain-display');

    Route::get('contents/rain-display/data', [RainDisplayController::class, 'data'])
        ->name('contents.rain-display.data');

    Route::get('contents/rain-readings', [RainDisplayController::class, 'readings'])
        ->name('contents.rain-readings');

    Route::get('contents/rain-graph-display', [RainGraphDisplayController::class, 'index'])
        ->name('contents.rain-graph-display');

    Route::get('contents/rain-graph-display/data', [RainGraphDisplayController::class, 'data'])
        ->name('contents.rain-graph-display.data');

    Route::get('contents/flood-display', [FloodDisplayController::class, 'index'])
        ->name('contents.flood-display');

    Route::get('contents/flood-display/data', [FloodDisplayController::class, 'data'])
        ->name('contents.flood-display.data');

    Route::get('contents/flood-readings', [FloodDisplayController::class, 'readings'])
        ->name('contents.flood-readings');

    Route::get('contents/flow-display', [FlowDisplayController::class, 'index'])
        ->name('contents.flow-display');

    Route::get('contents/flow-display/data', [FlowDisplayController::class, 'data'])
        ->name('contents.flow-display.data');

    Route::get('contents/flow-readings', [FlowDisplayController::class, 'readings'])
        ->name('contents.flow-readings');

    Route::get('contents/graph-display', [GraphDisplayController::class, 'index'])
        ->name('contents.graph-display');

    Route::get('contents/graph-display/data', [GraphDisplayController::class, 'data'])
        ->name('contents.graph-display.data');

    Route::get('contents/ai-insights', [AiInsightController::class, 'index'])
        ->name('contents.ai-insights');

    Route::get('contents/ai-chat', [AiChatController::class, 'index'])
        ->name('contents.ai-chat');

    Route::post('contents/ai-chat/ask', [AiChatController::class, 'ask'])
        ->name('contents.ai-chat.ask');

    Route::post('contents/ai-chat/clear', [AiChatController::class, 'clear'])
        ->name('contents.ai-chat.clear');

    Route::get('contents/notifications', [AlertNotificationController::class, 'index'])
        ->name('contents.notifications');

    Route::get('contents/notifications/history', [AlertNotificationController::class, 'history'])
        ->name('contents.notifications.history');

    Route::get('contents/announcements', [UserAnnouncementController::class, 'index'])
        ->name('contents.announcements');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::get('/notifications/{notification}/edit', [AdminNotificationController::class, 'edit'])->name('notifications.edit');
    Route::put('/notifications/{notification}', [AdminNotificationController::class, 'update'])->name('notifications.update');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/diagnostics/fcm-tokens', [AdminDiagnosticsController::class, 'fcmTokens'])->name('diagnostics.fcm-tokens');
});