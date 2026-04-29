<?php

use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\SensorIngestionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MobileAuthController;

Route::post('/app-login', [MobileAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('api.app-login');

// Existing FCM endpoints (kept for backward compatibility)
Route::post('/save-fcm-token', [FcmTokenController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('api.fcm-token.store');

Route::delete('/save-fcm-token', [FcmTokenController::class, 'destroy'])
    ->middleware('throttle:60,1')
    ->name('api.fcm-token.destroy');

// NEW: Android app endpoints (matches MainActivity.kt / MyFirebaseMessagingService.kt)
Route::post('/save-device-token', [FcmTokenController::class, 'storeFromApp'])
    ->middleware('throttle:60,1')
    ->name('api.device-token.store');

Route::delete('/save-device-token', [FcmTokenController::class, 'destroyFromApp'])
    ->middleware('throttle:60,1')
    ->name('api.device-token.destroy');

// Sensor ingestion (unchanged)
Route::post('/ingest/flow', [SensorIngestionController::class, 'storeFlow'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.flow');

Route::post('/ingest/rain', [SensorIngestionController::class, 'storeRain'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.rain');

Route::post('/ingest/flood', [SensorIngestionController::class, 'storeFlood'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.flood');
