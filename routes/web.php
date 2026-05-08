<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth'])->group(function () {
    // Redirect old /dashboard to x-change dashboard
    Route::redirect('dashboard', '/x/dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
