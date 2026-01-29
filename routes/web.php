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
use App\Livewire\Guardians\Children as GuardianChildren;

// Student Portal Routes - Dashboard is root route
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

// Guardian Portal Routes
Route::prefix('guardian')->name('guardian.')->group(function () {
    Route::get('/', GuardianChildren::class)->name('children');
    Route::get('/students/{student}/dashboard', Dashboard::class)->name('student.dashboard');
});