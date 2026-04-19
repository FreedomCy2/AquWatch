<?php

use App\Http\Controllers\SensorIngestionController;
use Illuminate\Support\Facades\Route;

Route::post('/ingest/flow', [SensorIngestionController::class, 'storeFlow'])
    ->name('api.ingest.flow');

Route::post('/ingest/rain', [SensorIngestionController::class, 'storeRain'])
    ->name('api.ingest.rain');

Route::post('/ingest/flood', [SensorIngestionController::class, 'storeFlood'])
    ->name('api.ingest.flood');
