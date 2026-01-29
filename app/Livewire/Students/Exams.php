<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Exams extends Component
{
    public function render()
    {
        return view('livewire.students.exams')
            ->layout('layouts.student');
    }
}
