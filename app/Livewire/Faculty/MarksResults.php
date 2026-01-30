<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarksResults extends Component
{
    public $teacher;
    public $selectedClass = null;
    public $selectedStream = null;
    public $selectedExam = null;
    public $selectedSubject = null;
    public array $classOptions = [];
    public array $streamOptions = [];
    public array $examOptions = [];
    public array $subjectOptions = [];
    public array $students = [];
    public array $marks = [];
    public array $resultSummary = [];

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

        if (count($this->classOptions) > 0 && ! $this->selectedClass) {
            $this->selectedClass = array_key_first($this->classOptions);
            $this->loadStreamsAndExams();
        }
    }

    public function loadStreamsAndExams(): void
    {
        if (! $this->selectedClass) {
            $this->streamOptions = [];
            $this->examOptions = [];
            $this->subjectOptions = [];
            $this->selectedStream = null;
            $this->selectedSubject = null;
            return;
        }

        // Load streams available for this class (based on teacher's assigned subjects)
        $this->streamOptions = DB::table('teacher_subjects')
            ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
            ->join('streams', 'streams.id', '=', 'subjects.stream_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->where('teacher_subjects.class_id', $this->selectedClass)
            ->distinct()
            ->pluck('streams.name', 'streams.id')
            ->all();

        // Load exams for this class
        $exams = DB::table('exams')
            ->where('class_id', $this->selectedClass)
            ->orderByDesc('academic_year')
            ->get(['id', 'name', 'academic_year']);
        $this->examOptions = $exams->mapWithKeys(fn ($e) => [$e->id => $e->name . ' (' . $e->academic_year . ')'])->all();

        // Auto-select first stream if available
        if (count($this->streamOptions) > 0 && ! $this->selectedStream) {
            $this->selectedStream = array_key_first($this->streamOptions);
        }
        if (count($this->examOptions) > 0 && ! $this->selectedExam) {
            $this->selectedExam = array_key_first($this->examOptions);
        }

        $this->loadSubjects();
    }

    public function loadSubjects(): void
    {
        if (! $this->selectedClass || ! $this->selectedStream) {
            $this->subjectOptions = [];
            $this->selectedSubject = null;
            return;
        }

        $this->subjectOptions = DB::table('teacher_subjects')
            ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->where('teacher_subjects.class_id', $this->selectedClass)
            ->where('subjects.stream_id', $this->selectedStream)
            ->pluck('subjects.name', 'teacher_subjects.subject_id')
            ->all();

        if (count($this->subjectOptions) > 0 && ! $this->selectedSubject) {
            $this->selectedSubject = array_key_first($this->subjectOptions);
        }
        $this->loadStudents();
    }

    public function loadStudents(): void
    {
        if (! $this->selectedClass || ! $this->selectedStream || ! $this->selectedSubject) {
            $this->students = [];
            $this->marks = [];
            return;
        }
        $this->students = DB::table('students')
            ->where('students.class_id', $this->selectedClass)
            ->where('students.stream_id', $this->selectedStream)
            ->select('students.id', 'students.roll_no', 'students.first_name', 'students.last_name')
            ->orderBy('students.roll_no')
            ->get()
            ->all();

        $existing = DB::table('marks')
            ->where('exam_id', $this->selectedExam)
            ->where('subject_id', $this->selectedSubject)
            ->pluck('marks_obtained', 'student_id')
            ->all();
        $this->marks = $existing;
    }

    public function updatedSelectedClass(): void
    {
        $this->selectedStream = null;
        $this->selectedSubject = null;
        $this->loadStreamsAndExams();
    }

    public function updatedSelectedStream(): void
    {
        $this->selectedSubject = null;
        $this->loadSubjects();
    }

    public function updatedSelectedExam(): void
    {
        $this->loadStudents();
    }

    public function updatedSelectedSubject(): void
    {
        $this->loadStudents();
    }

    public function saveAllMarks(): void
    {
        foreach ($this->students as $student) {
            $value = $this->marks[$student->id] ?? null;
            $this->saveMark($student->id, $value);
        }
        $this->loadStudents();
    }

    public function saveMark(int $studentId, $value): void
    {
        if (! $this->selectedExam || ! $this->selectedSubject) {
            return;
        }
        $value = $value !== null && $value !== '' ? (int) $value : null;
        if ($value === null) {
            DB::table('marks')
                ->where('student_id', $studentId)
                ->where('exam_id', $this->selectedExam)
                ->where('subject_id', $this->selectedSubject)
                ->delete();
            unset($this->marks[$studentId]);
        } else {
            DB::table('marks')->updateOrInsert(
                [
                    'student_id' => $studentId,
                    'exam_id' => $this->selectedExam,
                    'subject_id' => $this->selectedSubject,
                ],
                [
                    'marks_obtained' => $value,
                    'entered_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $this->marks[$studentId] = $value;
        }
        $this->loadResultSummary();
    }

    public function loadResultSummary(): void
    {
        if (! $this->selectedExam || ! $this->selectedSubject) {
            $this->resultSummary = [];
            return;
        }
        $rows = DB::table('marks')
            ->where('exam_id', $this->selectedExam)
            ->where('subject_id', $this->selectedSubject)
            ->get();
        $this->resultSummary = [
            'total_students' => count($this->students),
            'marks_entered' => $rows->count(),
            'average' => $rows->count() > 0 ? round($rows->avg('marks_obtained'), 2) : 0,
            'highest' => $rows->max('marks_obtained') ?? '-',
            'lowest' => $rows->min('marks_obtained') ?? '-',
        ];
    }

    public function render()
    {
        $this->loadResultSummary();
        return view('livewire.faculty.marks-results')
            ->layout('layouts.faculty');
    }
}
