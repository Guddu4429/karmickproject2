<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Students;
use App\Livewire\Students\CreateStudent;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/students', Students::class)
    ->name('students');

Route::get('/students/create', CreateStudent::class)
    ->name('students.create');