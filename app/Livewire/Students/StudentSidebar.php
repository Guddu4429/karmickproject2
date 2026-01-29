<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public bool $isGuardian = false;
    public ?int $studentId = null;

    public function mount($activeMenu = 'dashboard')
    {
        $this->activeMenu = $activeMenu;

        if ($user = Auth::user()) {
            $roleName = DB::table('roles')->where('id', $user->role_id)->value('id') ? DB::table('roles')->where('id', $user->role_id)->value('name') : null;
            $this->isGuardian = $roleName === 'Guardian';

            if ($this->isGuardian) {
                $this->studentId = (int) (session('active_student_id') ?? 0) ?: null;
            }
        }
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
        return view('livewire.students.student-sidebar', [
            'isGuardian' => $this->isGuardian,
        ]);
    }
}
