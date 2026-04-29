<?php

use App\Http\Controllers\SensorIngestionController;
use Illuminate\Support\Facades\Route;

// NOTE: The Android app no longer uses a separate mobile login or a public
// device-token endpoint. It relies on the VPS web login (shared WebView cookie)
// and registers its FCM token through the authenticated web route
// POST /fcm/token (see routes/web.php and resources/views/.../settings-edit.blade.php).

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