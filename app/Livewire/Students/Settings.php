<?php

namespace App\Livewire\Students;

use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.students.settings')
            ->layout('layouts.student');
    }
}
