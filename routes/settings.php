<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'restrict.admin.panel'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
    Route::redirect('settings/notifications', 'settings/profile');
    Route::redirect('settings/appearance', 'settings/profile');
    Route::redirect('settings/two-factor', 'settings/profile');
});

Route::middleware(['auth', 'verified', 'restrict.admin.panel'])->group(function () {
    Route::livewire('settings/password', 'pages::settings.password')->name('user-password.edit');
});
