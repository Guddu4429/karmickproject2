<?php

namespace App\Livewire\Students;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.students.profile')
            ->layout('layouts.student');
    }
}
