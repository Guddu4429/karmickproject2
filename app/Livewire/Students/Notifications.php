<?php

namespace App\Livewire\Students;

use Livewire\Component;

class Notifications extends Component
{
    public function render()
    {
        return view('livewire.students.notifications')
            ->layout('layouts.student');
    }
}
