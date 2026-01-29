<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Student Sidebar Component
 * 
 * Reusable sidebar navigation component for student pages.
 * 
 * Usage:
 * <livewire:students.student-sidebar active-menu="dashboard" />
 * 
 * Available active-menu values:
 * - dashboard
 * - profile
 * - attendance
 * - fees
 * - exams
 * - notifications
 * - settings
 */
class StudentSidebar extends Component
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
        return view('livewire.students.student-sidebar');
    }
}
