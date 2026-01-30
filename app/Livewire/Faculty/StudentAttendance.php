<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAttendance extends Component
{
    public $teacher;
    public $selectedClass = null;
    public $selectedSubject = null;
    public $selectedDate;
    public array $classOptions = [];
    public array $subjectOptions = [];
    public array $students = [];
    public array $attendanceRecords = [];
    public $viewMode = 'mark'; // 'mark' | 'view' | 'report'

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (! $this->teacher) {
            return;
        }

        $this->selectedDate = now()->toDateString();

        $this->classOptions = DB::table('teacher_subjects')
            ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->distinct()
            ->pluck('classes.name', 'teacher_subjects.class_id')
            ->all();

        if (count($this->classOptions) > 0 && ! $this->selectedClass) {
            $this->selectedClass = array_key_first($this->classOptions);
            $this->loadSubjectOptions();
        }
    }

    public function loadSubjectOptions(): void
    {
        if (! $this->selectedClass) {
            $this->subjectOptions = [];
            return;
        }
        $this->subjectOptions = DB::table('teacher_subjects')
            ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->where('teacher_subjects.class_id', $this->selectedClass)
            ->pluck('subjects.name', 'teacher_subjects.subject_id')
            ->all();
        $this->selectedSubject = count($this->subjectOptions) > 0 ? array_key_first($this->subjectOptions) : null;
        $this->loadStudents();
    }

    public function loadStudents(): void
    {
        if (! $this->selectedClass) {
            $this->students = [];
            return;
        }
        $query = DB::table('students')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->where('students.class_id', $this->selectedClass)
            ->select('students.id', 'students.roll_no', 'students.first_name', 'students.last_name', 'streams.name as stream_name');

        if ($this->selectedSubject) {
            $subject = DB::table('subjects')->where('id', $this->selectedSubject)->first();
            if ($subject) {
                $query->where('students.stream_id', $subject->stream_id);
            }
        }

        $this->students = $query->orderBy('students.roll_no')->get()->all();
        $this->loadAttendanceRecords();
    }

    public function loadAttendanceRecords(): void
    {
        if (! $this->selectedSubject || ! $this->selectedDate) {
            $this->attendanceRecords = [];
            return;
        }
        $records = DB::table('attendance')
            ->where('subject_id', $this->selectedSubject)
            ->where('teacher_id', $this->teacher->id)
            ->where('date', $this->selectedDate)
            ->pluck('status', 'student_id')
            ->all();
        $this->attendanceRecords = $records;
    }

    public function updatedSelectedClass(): void
    {
        $this->loadSubjectOptions();
    }

    public function updatedSelectedSubject(): void
    {
        $this->loadStudents();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadAttendanceRecords();
    }

    public function markAttendance(int $studentId, string $status): void
    {
        if (! $this->selectedSubject || ! $this->selectedDate) {
            return;
        }
        DB::table('attendance')->updateOrInsert(
            [
                'student_id' => $studentId,
                'subject_id' => $this->selectedSubject,
                'teacher_id' => $this->teacher->id,
                'date' => $this->selectedDate,
            ],
            [
                'status' => $status,
                'updated_at' => now(),
            ]
        );
        $this->attendanceRecords[$studentId] = $status;
        $this->dispatch('attendance-updated');
    }

    public function getMonthlyReportProperty()
    {
        if (! $this->selectedClass || ! $this->selectedSubject) {
            return [];
        }
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();
        return DB::table('attendance')
            ->where('subject_id', $this->selectedSubject)
            ->where('teacher_id', $this->teacher->id)
            ->whereBetween('date', [$start, $end])
            ->selectRaw('student_id, COUNT(*) as total, SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present')
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id')
            ->all();
    }

    public function render()
    {
        return view('livewire.faculty.student-attendance')
            ->layout('layouts.faculty');
    }
}
