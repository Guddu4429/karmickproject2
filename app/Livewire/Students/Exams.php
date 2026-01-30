<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Exams extends Component
{
    public $student = null;
    public $selectedYear = null;
    public $selectedExamName = null;
    public array $yearOptions = [];
    public array $examNameOptions = [];
    public ?object $selectedResult = null;
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

        // Find the latest result (same logic as Dashboard)
        $latestResult = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $student->id)
            ->orderByDesc('exams.academic_year')
            ->orderByDesc('results.id')
            ->select([
                'results.exam_id',
                'exams.academic_year',
            ])
            ->first();

        // Load distinct academic years from exams that have results for this student
        $years = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $student->id)
            ->distinct()
            ->orderByDesc('exams.academic_year')
            ->pluck('exams.academic_year');

        $this->yearOptions = $years->all();

        // Select the latest year from the latest result by default
        if ($latestResult && ! $this->selectedYear) {
            $this->selectedYear = $latestResult->academic_year;
        } elseif (count($this->yearOptions) > 0 && ! $this->selectedYear) {
            $this->selectedYear = $this->yearOptions[0];
        }

        // Load exam names for the selected year
        $this->loadExamNames();

        // Select the latest exam from the latest result by default
        if ($latestResult && ! $this->selectedExamName) {
            $this->selectedExamName = $latestResult->exam_id;
        }

        // Load exam details
        $this->loadExamDetails();

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

    public function loadExamNames(): void
    {
        if (! $this->student || ! $this->selectedYear) {
            $this->examNameOptions = [];
            $this->selectedExamName = null;
            return;
        }

        // Load exam names for selected year that have results for this student
        $exams = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $this->student->id)
            ->where('exams.academic_year', $this->selectedYear)
            ->distinct()
            ->orderByDesc('results.id')
            ->get(['exams.id', 'exams.name']);

        $this->examNameOptions = $exams->mapWithKeys(fn ($e) => [$e->id => $e->name])->all();

        // Select the latest exam name by default (first one from results ordered by id desc)
        if (count($this->examNameOptions) > 0 && ! $this->selectedExamName) {
            $this->selectedExamName = array_key_first($this->examNameOptions);
        }
    }

    public function loadExamDetails(): void
    {
        if (! $this->student || ! $this->selectedExamName) {
            $this->selectedResult = null;
            $this->subjectMarks = [];
            return;
        }

        // Load result for selected exam
        $this->selectedResult = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->where('results.student_id', $this->student->id)
            ->where('results.exam_id', $this->selectedExamName)
            ->select([
                'results.*',
                'exams.name as exam_name',
                'exams.academic_year',
            ])
            ->first();

        // Load subject-wise marks for selected exam
        $this->subjectMarks = DB::table('marks')
            ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
            ->where('marks.student_id', $this->student->id)
            ->where('marks.exam_id', $this->selectedExamName)
            ->select([
                'subjects.name as subject_name',
                'marks.marks_obtained',
            ])
            ->get()
            ->map(fn ($row) => [
                'subject_name' => $row->subject_name,
                'marks' => $row->marks_obtained,
            ])
            ->all();
    }

    public function updatedSelectedYear(): void
    {
        $this->selectedExamName = null;
        $this->loadExamNames();
        $this->loadExamDetails();
    }

    public function updatedSelectedExamName(): void
    {
        $this->loadExamDetails();
    }

    public function render()
    {
        return view('livewire.students.exams')
            ->layout('layouts.student');
    }
}
