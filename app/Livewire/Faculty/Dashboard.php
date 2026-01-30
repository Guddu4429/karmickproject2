<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $teacher;
    public array $assignedSubjects = [];
    public ?object $todayAttendance = null;
    public int $pendingMarksCount = 0;
    public int $todayStudentAttendanceMarked = 0;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isFaculty()) {
            return;
        }

        $this->teacher = DB::table('teachers')
            ->where('user_id', $user->id)
            ->first();

        if (! $this->teacher) {
            return;
        }

        // Assigned subjects with class & stream
        $this->assignedSubjects = DB::table('teacher_subjects')
            ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
            ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'subjects.stream_id')
            ->where('teacher_subjects.teacher_id', $this->teacher->id)
            ->select([
                'subjects.name as subject_name',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->get()
            ->map(fn ($r) => (object) [
                'subject_name' => $r->subject_name,
                'class_name' => $r->class_name,
                'stream_name' => $r->stream_name ?? '-',
            ])
            ->all();

        // Today's teacher attendance (check-in/check-out)
        $this->todayAttendance = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        // Pending marks - exams in assigned classes without full marks (simplified indicator)
        $assignedSubjectIds = DB::table('teacher_subjects')
            ->where('teacher_id', $this->teacher->id)
            ->pluck('subject_id');
        $recentExams = DB::table('exams')
            ->where('academic_year', now()->year)
            ->pluck('id');
        $marksEntered = DB::table('marks')
            ->whereIn('subject_id', $assignedSubjectIds)
            ->whereIn('exam_id', $recentExams)
            ->where('entered_by', Auth::id())
            ->count();
        $this->pendingMarksCount = max(0, $assignedSubjectIds->count() * 5 - $marksEntered); // simple indicator

        // Today's student attendance marked by this teacher
        $this->todayStudentAttendanceMarked = DB::table('attendance')
            ->where('teacher_id', $this->teacher->id)
            ->where('date', now()->toDateString())
            ->count();
    }

    public function render()
    {
        return view('livewire.faculty.dashboard')
            ->layout('layouts.faculty');
    }
}
