<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class FacultySidebar extends Component
{
    public $activeMenu;

    public function mount($activeMenu = 'dashboard')
    {
        $this->activeMenu = $activeMenu;
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.faculty.faculty-sidebar');
    }
}
