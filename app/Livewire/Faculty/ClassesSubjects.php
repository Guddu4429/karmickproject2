<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassesSubjects extends Component
{
    public $teacher;
    public $selectedClass = null;
    public array $assignments = [];
    public array $students = [];
    public array $classOptions = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (! $this->teacher) {
            return;
        }

        $this->assignments = DB::table('teacher_subjects')
            ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
            ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'subjects.stream_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->select([
                'teacher_subjects.class_id',
                'teacher_subjects.subject_id',
                'subjects.name as subject_name',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->orderBy('classes.name')
            ->orderBy('subjects.name')
            ->get()
            ->all();

        $this->classOptions = DB::table('teacher_subjects')
            ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->distinct()
            ->pluck('classes.name', 'teacher_subjects.class_id')
            ->all();

        if (count($this->classOptions) > 0 && ! $this->selectedClass) {
            $this->selectedClass = array_key_first($this->classOptions);
            $this->loadStudents();
        }
    }

    public function loadStudents(): void
    {
        if (! $this->selectedClass) {
            $this->students = [];
            return;
        }
        $this->students = DB::table('students')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->where('students.class_id', $this->selectedClass)
            ->select('students.id', 'students.roll_no', 'students.first_name', 'students.last_name', 'streams.name as stream_name')
            ->orderBy('students.stream_id')
            ->orderBy('students.roll_no')
            ->get()
            ->all();
    }

    public function updatedSelectedClass(): void
    {
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.faculty.classes-subjects')
            ->layout('layouts.faculty');
    }
}
