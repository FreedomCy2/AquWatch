<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlowDisplayController;
use App\Http\Controllers\FloodDisplayController;
use App\Http\Controllers\GraphDisplayController;
use App\Http\Controllers\RainDisplayController;
use App\Http\Controllers\RainGraphDisplayController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/plans', 'plans')->name('plans');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.legacy.edit');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.legacy.update');
        Route::get('/account/settings', [SettingsController::class, 'edit'])->name('account.settings.edit');
        Route::post('/account/settings', [SettingsController::class, 'update'])->name('account.settings.update');


                Route::redirect('/rain-display', '/contents/rain-display');
                Route::redirect('/flood-display', '/contents/flood-display');
                Route::redirect('/flow-display', '/contents/flow-display');
                Route::redirect('/graph-display', '/contents/graph-display');


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
});