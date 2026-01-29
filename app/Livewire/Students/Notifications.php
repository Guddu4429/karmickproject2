<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public function render()
    {
        return view('livewire.students.notifications')
            ->layout('layouts.student');
    }
}
