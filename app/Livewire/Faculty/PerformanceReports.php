<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerformanceReports extends Component
{
    public $teacher;
    public $selectedClass = null;
    public $selectedExam = null;
    public array $classOptions = [];
    public array $examOptions = [];
    public array $subjectWisePerformance = [];
    public array $weakStudents = [];
    public ?object $classWiseSummary = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (! $this->teacher) {
            return;
        }

        $this->classOptions = DB::table('teacher_subjects')
            ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->distinct()
            ->pluck('classes.name', 'teacher_subjects.class_id')
            ->all();

        if (count($this->classOptions) > 0) {
            $this->selectedClass = array_key_first($this->classOptions);
            $this->loadExamOptions();
        }
    }

    public function loadExamOptions(): void
    {
        if (! $this->selectedClass) {
            $this->examOptions = [];
            $this->selectedExam = null;
            return;
        }
        $exams = DB::table('exams')
            ->where('class_id', $this->selectedClass)
            ->orderByDesc('academic_year')
            ->get(['id', 'name', 'academic_year']);
        $this->examOptions = $exams->mapWithKeys(fn ($e) => [$e->id => $e->name . ' (' . $e->academic_year . ')'])->all();
        $this->selectedExam = count($this->examOptions) > 0 ? array_key_first($this->examOptions) : null;
        $this->loadReports();
    }

    public function loadReports(): void
    {
        if (! $this->selectedClass || ! $this->selectedExam) {
            $this->subjectWisePerformance = [];
            $this->weakStudents = [];
            $this->classWiseSummary = null;
            return;
        }

        $subjectIds = DB::table('teacher_subjects')
            ->where('teacher_id', $this->teacher->id)
            ->where('class_id', $this->selectedClass)
            ->pluck('subject_id');

        $this->subjectWisePerformance = DB::table('marks')
            ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
            ->where('marks.exam_id', $this->selectedExam)
            ->whereIn('marks.subject_id', $subjectIds)
            ->groupBy('marks.subject_id', 'subjects.name')
            ->selectRaw('subjects.name as subject_name, AVG(marks_obtained) as avg_marks, COUNT(*) as student_count')
            ->get()
            ->map(fn ($r) => (object) [
                'subject_name' => $r->subject_name,
                'avg_marks' => round($r->avg_marks, 2),
                'student_count' => $r->student_count,
            ])
            ->all();

        $this->weakStudents = DB::table('marks')
            ->join('students', 'students.id', '=', 'marks.student_id')
            ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
            ->where('marks.exam_id', $this->selectedExam)
            ->whereIn('marks.subject_id', $subjectIds)
            ->where('marks.marks_obtained', '<', 40)
            ->select('students.roll_no', 'students.first_name', 'students.last_name', 'subjects.name as subject_name', 'marks.marks_obtained')
            ->orderBy('marks.marks_obtained')
            ->limit(20)
            ->get()
            ->all();

        $summary = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->join('students', 'students.id', '=', 'results.student_id')
            ->where('results.exam_id', $this->selectedExam)
            ->where('students.class_id', $this->selectedClass)
            ->selectRaw('AVG(results.percentage) as avg_pct, COUNT(*) as student_count')
            ->first();
        $this->classWiseSummary = $summary ?? (object) ['avg_pct' => 0.0, 'student_count' => 0];
    }

    public function updatedSelectedClass(): void
    {
        $this->loadExamOptions();
    }

    public function updatedSelectedExam(): void
    {
        $this->loadReports();
    }

    public function render()
    {
        return view('livewire.faculty.performance-reports')
            ->layout('layouts.faculty');
    }
}
