<?php

namespace App\Livewire\Students;

use Livewire\Component;

class Fees extends Component
{
    public function render()
    {
        return view('livewire.students.fees')
            ->layout('layouts.student');
    }
}
