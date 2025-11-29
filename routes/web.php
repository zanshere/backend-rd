<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard based on role
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return view('dashboard-admin');
    } else {
        return view('dashboard');
    }
})->middleware(['auth', 'verified'])
  ->name('dashboard');

// Public routes for ordering
Route::view('/order', 'order-landing')
    ->name('landing-page');

Route::view('/packages', 'packages')
    ->name('packages');

Route::view('/help', 'help')
    ->name('help');

// Authentication routes group
Route::middleware(['auth'])->group(function () {
    // Settings routes (existing)
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')
        ->name('profile.edit');

    Volt::route('settings/password', 'settings.password')
        ->name('user-password.edit');

    Volt::route('settings/appearance', 'settings.appearance')
        ->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // User routes - menggunakan role:user
    Route::middleware(['role:user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', \App\Livewire\User\Dashboard::class)->name('dashboard');
        Route::get('/orders', \App\Livewire\User\Orders::class)->name('orders');
        Route::get('/orders/{order}', \App\Livewire\User\OrderDetail::class)->name('order-detail');
        Route::get('/history', \App\Livewire\User\History::class)->name('history');
        Route::get('/feedback', \App\Livewire\User\Feedback::class)->name('feedback');
    });

    // Admin routes - menggunakan role:admin
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('users');
        Route::get('/orders', \App\Livewire\Admin\OrderManagement::class)->name('orders');
        Route::get('/orders/{order}/progress', \App\Livewire\Admin\OrderProgress::class)->name('order-progress');
        Route::get('/feedbacks', \App\Livewire\Admin\FeedbackManagement::class)->name('feedbacks');
        Route::get('/analytics', \App\Livewire\Admin\Analytics::class)->name('analytics');
    });

    // Common routes for both roles - menggunakan role:user,admin
    Route::middleware(['role:user,admin'])->group(function () {
        Route::get('/notifications', \App\Livewire\Common\Notifications::class)->name('notifications');
        Route::get('/messages', \App\Livewire\Common\Messages::class)->name('messages');
    });
});
