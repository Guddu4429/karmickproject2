<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Students;
use App\Livewire\Students\CreateStudent;
use App\Livewire\Students\Dashboard;
use App\Livewire\Students\Profile;
use App\Livewire\Students\Attendance;
use App\Livewire\Students\Fees;
use App\Livewire\Students\Exams;
use App\Livewire\Students\Notifications;
use App\Livewire\Students\Settings;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/students', Students::class)
    ->name('students');

Route::get('/students/create', CreateStudent::class)
    ->name('students.create');

// Student Portal Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/attendance', Attendance::class)->name('attendance');
    Route::get('/fees', Fees::class)->name('fees');
    Route::get('/exams', Exams::class)->name('exams');
    Route::get('/notifications', Notifications::class)->name('notifications');
    Route::get('/settings', Settings::class)->name('settings');
});