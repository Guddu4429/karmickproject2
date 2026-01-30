<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsPdf extends Component
{
    public $teacher;
    public array $reports = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();

        $this->reports = [
            ['name' => 'Attendance Report', 'route' => 'faculty.attendance', 'icon' => 'bi-calendar-check', 'desc' => 'View and export student attendance'],
            ['name' => 'Result Sheets', 'route' => 'faculty.marks', 'icon' => 'bi-journal-text', 'desc' => 'View exam results and marksheets'],
            ['name' => 'Class Reports', 'route' => 'faculty.classes', 'icon' => 'bi-people', 'desc' => 'Class-wise student lists and reports'],
            ['name' => 'Performance Report', 'route' => 'faculty.performance', 'icon' => 'bi-graph-up', 'desc' => 'Subject-wise and class-wise analytics'],
        ];
    }

    public function render()
    {
        return view('livewire.faculty.reports-pdf')
            ->layout('layouts.faculty');
    }
}
