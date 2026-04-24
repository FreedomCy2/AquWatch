<?php

use App\Http\Controllers\SensorIngestionController;
use Illuminate\Support\Facades\Route;

Route::post('/ingest/flow', [SensorIngestionController::class, 'storeFlow'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.flow');

Route::post('/ingest/rain', [SensorIngestionController::class, 'storeRain'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.rain');

Route::post('/ingest/flood', [SensorIngestionController::class, 'storeFlood'])
    ->middleware('throttle:sensor-ingest')
    ->name('api.ingest.flood');
