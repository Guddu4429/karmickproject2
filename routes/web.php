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
use App\Livewire\Students\Enquiry;
use App\Livewire\Students\Settings;
use App\Livewire\Guardians\Children as GuardianChildren;
use App\Livewire\Faculty\Dashboard as FacultyDashboard;
use App\Livewire\Faculty\StudentAttendance as FacultyStudentAttendance;
use App\Livewire\Faculty\CheckInOut;
use App\Livewire\Faculty\MarksResults;
use App\Livewire\Faculty\ClassesSubjects;
use App\Livewire\Faculty\PerformanceReports;
use App\Livewire\Faculty\ReportsPdf;
use App\Livewire\Faculty\Notifications as FacultyNotifications;
use App\Livewire\Faculty\Profile as FacultyProfile;
use App\Http\Controllers\MarksheetController;

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
    Route::get('/', Dashboard::class)->name('student.dashboard')->middleware('redirect.faculty');

    Route::get('/students', Students::class)
        ->name('students');


    // Other Student Portal Routes
    Route::prefix('student')->name('student.')->group(function () {
        // Optional {student} parameter so guardians can have /student/{id}/... URLs,
        // while non-guardian users can still hit /student/... without an id.
        Route::get('/{student?}/profile', Profile::class)->name('profile');
        Route::get('/{student?}/attendance', Attendance::class)->name('attendance');
        Route::get('/{student?}/fees',        Fees::class)->name('fees');
        Route::get('/{student?}/exams',       Exams::class)->name('exams');
        Route::get('/{student?}/notifications', Notifications::class)->name('notifications');
        Route::get('/{student?}/enquiry',     Enquiry::class)->name('enquiry');
        Route::get('/{student?}/settings',    Settings::class)->name('settings');
    });

    // Guardian Portal Routes
    Route::prefix('guardian')->name('guardian.')->group(function () {
        // Show children cards (no sidebar) as guardian home
        Route::get('/', GuardianChildren::class)->name('children');
        // Open specific student's dashboard
        Route::get('/students/{student}/dashboard', Dashboard::class)->name('student.dashboard');
    });

    // Faculty Portal Routes (Protected - Faculty only)
    Route::middleware('faculty')->prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/', FacultyDashboard::class)->name('dashboard');
        Route::get('/attendance', FacultyStudentAttendance::class)->name('attendance');
        Route::get('/check-in', CheckInOut::class)->name('checkin');
        Route::get('/marks', MarksResults::class)->name('marks');
        Route::get('/classes', ClassesSubjects::class)->name('classes');
        Route::get('/performance', PerformanceReports::class)->name('performance');
        Route::get('/reports', ReportsPdf::class)->name('reports');
        Route::get('/notifications', FacultyNotifications::class)->name('notifications');
        Route::get('/profile', FacultyProfile::class)->name('profile');
    });

    // Marksheet Download Routes
    Route::get('/marksheet/{resultId}/download', [MarksheetController::class, 'download'])->name('marksheet.download');
    Route::get('/marksheet/latest/download', [MarksheetController::class, 'downloadLatest'])->name('marksheet.download.latest');
});