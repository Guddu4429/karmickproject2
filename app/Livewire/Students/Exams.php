<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Exams extends Component
{
    public $student = null;
    public ?object $latestResult = null;
    public array $subjectMarks = [];
    public array $upcomingExams = [];
    public array $marksheets = [];

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
                'students.class_id',
                'students.stream_id',
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

        // Latest result (if any)
        $latestResult = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $student->id)
            ->orderByDesc('results.id')
            ->select([
                'results.*',
                'exams.name as exam_name',
                'exams.academic_year',
            ])
            ->first();

        $this->latestResult = $latestResult;

        // Subject-wise marks for latest exam
        if ($latestResult) {
            $this->subjectMarks = DB::table('marks')
                ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
                ->where('marks.student_id', $student->id)
                ->where('marks.exam_id', $latestResult->exam_id)
                ->select([
                    'subjects.name as subject_name',
                    'marks.marks_obtained',
                ])
                ->get()
                ->map(function ($row) {
                    return [
                        'subject_name' => $row->subject_name,
                        'marks' => $row->marks_obtained,
                    ];
                })
                ->all();
        }

        // Upcoming exams = exams for student's class in future academic year (simple demo)
        $this->upcomingExams = DB::table('exams')
            ->where('class_id', $student->class_id)
            ->orderBy('name')
            ->get()
            ->map(function ($exam) {
                return [
                    'name' => $exam->name,
                    'academic_year' => $exam->academic_year,
                ];
            })
            ->all();

        // Marksheets (all results for student)
        $this->marksheets = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $student->id)
            ->orderByDesc('exams.academic_year')
            ->orderByDesc('results.id')
            ->select([
                'results.id',
                'exams.name as exam_name',
                'exams.academic_year',
                'results.percentage',
                'results.grade',
            ])
            ->get()
            ->all();
    }

    public function render()
    {
        return view('livewire.students.exams')
            ->layout('layouts.student');
    }
}
