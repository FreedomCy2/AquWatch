<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');


    Route::view('/rain-display', 'rain-display');
    Route::view('/flood-display', 'flood-display');
    Route::view('/flow-display', 'flow-display');
    Route::view('/graph-display', 'graph-display');


Route::get('contents/rain-display', function () {
        return view('contents.rain-display');
})->name('contents.rain-display');

Route::get('contents/flood-display', function () {
        return view('contents.flood-display');
})->name('contents.flood-display');

Route::get('contents/flow-display', function () {
        return view('contents.flow-display');
})->name('contents.flow-display');

Route::get('contents/graph-display', function () {
        return view('contents.graph-display');
})->name('contents.graph-display');
});