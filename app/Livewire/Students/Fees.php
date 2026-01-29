<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Fees extends Component
{
    public function render()
    {
        return view('livewire.students.fees')
            ->layout('layouts.student');
    }
}
