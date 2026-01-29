<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Attendance extends Component
{
    public function render()
    {
        return view('livewire.students.attendance')
            ->layout('layouts.student');
    }
}
