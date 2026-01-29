<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Attendance extends Component
{
    public $student = null;
    public array $summary = [];
    public array $subjectWise = [];

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName !== 'Guardian') {
            return;
        }

        $studentId = session('active_student_id');
        if (! $studentId) {
            $this->redirect(route('guardian.children'), navigate: true);
            return;
        }

        $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');

        $student = DB::table('students')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->where('students.id', $studentId)
            ->where('students.guardian_id', $guardianId)
            ->select([
                'students.id',
                'students.first_name',
                'students.last_name',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->first();

        if (! $student) {
            abort(403, 'You are not allowed to view this student.');
        }

        $this->student = $student;

        // Overall attendance
        $total = DB::table('attendance')
            ->where('student_id', $student->id)
            ->count();

        $present = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('status', 'Present')
            ->count();

        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
        $cutoff = 75;

        $this->summary = [
            'total' => $total,
            'present' => $present,
            'percentage' => $percentage,
            'cutoff' => $cutoff,
            'status' => $percentage >= $cutoff ? 'Eligible' : 'Not Eligible',
        ];

        // Subject-wise attendance
        $rows = DB::table('attendance')
            ->join('subjects', 'subjects.id', '=', 'attendance.subject_id')
            ->where('attendance.student_id', $student->id)
            ->groupBy('attendance.subject_id', 'subjects.name')
            ->selectRaw("subjects.name as subject_name, COUNT(*) as total_classes, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as attended")
            ->get();

        $this->subjectWise = $rows->map(function ($row) use ($cutoff) {
            $pct = $row->total_classes > 0 ? round(($row->attended / $row->total_classes) * 100, 2) : 0;
            return [
                'subject_name' => $row->subject_name,
                'total_classes' => $row->total_classes,
                'attended' => $row->attended,
                'percentage' => $pct,
                'status' => $pct >= $cutoff ? 'Above Cutoff' : 'Below Cutoff',
            ];
        })->all();
    }

    public function render()
    {
        return view('livewire.students.attendance')
            ->layout('layouts.student');
    }
}
