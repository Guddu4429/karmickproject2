<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Auth\Login as AuthLogin;
use App\Livewire\Students;
use App\Livewire\Students\CreateStudent;
use App\Livewire\Students\Dashboard;
use App\Livewire\Students\Profile;
use App\Livewire\Students\Attendance;
use App\Livewire\Students\Fees;
use App\Livewire\Students\Exams;
use App\Livewire\Students\Notifications;
use App\Livewire\Students\Settings;
use App\Livewire\Guardians\Children as GuardianChildren;

// Authentication Routes (Public)
Route::get('/login', AuthLogin::class)->name('login');

// Login form routes (Public - No auth required, but guest middleware)
Route::middleware('guest')->group(function () {
    Route::get('/login/{type}', [LoginController::class, 'show'])->name('login.form');
    Route::post('/login/{type}', [LoginController::class, 'login'])->name('login.submit');
});

// Sign-in routes (Public - No auth required)
Route::get('/signin', [SignInController::class, 'create'])->name('signin.create');
Route::post('/signin', [SignInController::class, 'store'])->name('signin.store');

// Student Portal Routes (Protected - Authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('student.dashboard');

    Route::get('/students', Students::class)
        ->name('students');

    Route::get('/students/create', CreateStudent::class)
        ->name('students.create');

    // Other Student Portal Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/attendance', Attendance::class)->name('attendance');
        Route::get('/fees', Fees::class)->name('fees');
        Route::get('/exams', Exams::class)->name('exams');
        Route::get('/notifications', Notifications::class)->name('notifications');
        Route::get('/settings', Settings::class)->name('settings');
    });

    Route::prefix('guardian')->name('guardian.')->group(function () {
    Route::get('/', GuardianChildren::class)->name('children');
    Route::get('/students/{student}/dashboard', Dashboard::class)->name('student.dashboard');
    });
});