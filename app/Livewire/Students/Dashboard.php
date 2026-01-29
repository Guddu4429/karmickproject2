<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public ?int $studentId = null;

    public array $attendance = [];
    public array $fees = [];
    public ?object $latestResult = null;
    public array $subjectPerformance = [];

    public function mount($student = null): void
    {
        // If guardian opens /guardian/students/{student}/dashboard we receive {student} here.
        if ($student !== null) {
            $this->studentId = (int) $student;

            // Remember the active student for this guardian session
            session(['active_student_id' => $this->studentId]);
        }

        // If a guardian hits "/" directly, send them to their children picker.
        $user = Auth::user();
        if ($user) {
            $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
            if ($roleName === 'Guardian' && $this->studentId === null) {
                // Try to use previously selected student from session
                $sessionStudentId = session('active_student_id');
                if ($sessionStudentId) {
                    $this->studentId = (int) $sessionStudentId;
                } else {
                    $this->redirect(route('guardian.children'), navigate: true);
                }
            }
        }
    }

    public function render()
    {
        $student = null;

        if ($this->studentId) {
            // Guardian ownership check (guardian can only view their own children)
            $user = Auth::user();
            if ($user) {
                $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');

                $student = DB::table('students')
                    ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
                    ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
                    ->where('students.id', $this->studentId)
                    ->where('students.guardian_id', $guardianId)
                    ->select([
                        'students.id',
                        'students.class_id',
                        'students.stream_id',
                        'students.first_name',
                        'students.last_name',
                        'students.admission_no',
                        'students.roll_no',
                        'classes.name as class_name',
                        'streams.name as stream_name',
                    ])
                    ->first();

                if (! $student) {
                    abort(403, 'You are not allowed to view this student.');
                }

                // ----- KPI: Attendance summary -----
                $total = DB::table('attendance')
                    ->where('student_id', $student->id)
                    ->count();

                $present = DB::table('attendance')
                    ->where('student_id', $student->id)
                    ->where('status', 'Present')
                    ->count();

                $attendancePct = $total > 0 ? round(($present / $total) * 100, 2) : null;

                $this->attendance = [
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $attendancePct,
                ];

                // ----- KPI: Fees summary -----
                $annualTotal = 90000.0; // demo value
                $paid = (float) DB::table('fee_payments')
                    ->where('student_id', $student->id)
                    ->sum('amount');
                $due = max(0, $annualTotal - $paid);

                $this->fees = [
                    'annual_total' => $annualTotal,
                    'paid' => $paid,
                    'due' => $due,
                ];

                // ----- Latest result & grade -----
                $latestResult = DB::table('results')
                    ->join('exams', 'exams.id', '=', 'results.exam_id')
                    ->where('results.student_id', $student->id)
                    ->orderByDesc('exams.academic_year')
                    ->orderByDesc('results.id')
                    ->select([
                        'results.*',
                        'exams.name as exam_name',
                        'exams.academic_year',
                    ])
                    ->first();

                $this->latestResult = $latestResult;

                // ----- Academic performance table (Unit / Half / Annual per subject) -----
                $performanceRows = DB::table('subjects')
                    ->leftJoin('marks', function ($join) use ($student) {
                        $join->on('marks.subject_id', '=', 'subjects.id')
                             ->where('marks.student_id', $student->id);
                    })
                    ->leftJoin('exams', 'exams.id', '=', 'marks.exam_id')
                    ->where('subjects.class_id', $student->class_id)
                    ->where('subjects.stream_id', $student->stream_id)
                    ->groupBy('subjects.id', 'subjects.name')
                    ->selectRaw("
                        subjects.name as subject_name,
                        MAX(CASE WHEN exams.name = 'Unit Test' THEN marks.marks_obtained END) as unit_test,
                        MAX(CASE WHEN exams.name = 'Half-Yearly' THEN marks.marks_obtained END) as half_yearly,
                        MAX(CASE WHEN exams.name = 'Annual' THEN marks.marks_obtained END) as annual
                    ")
                    ->get();

                $this->subjectPerformance = $performanceRows->map(function ($row) {
                    return [
                        'subject_name' => $row->subject_name,
                        'unit_test' => $row->unit_test,
                        'half_yearly' => $row->half_yearly,
                        'annual' => $row->annual,
                    ];
                })->all();
            }
        }

        // Determine if user is guardian and get student ID for route generation
        $isGuardian = false;
        $studentIdForRoute = null;
        $user = Auth::user();
        if ($user) {
            $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
            $isGuardian = $roleName === 'Guardian';
            if ($isGuardian && $this->studentId) {
                $studentIdForRoute = $this->studentId;
            }
        }

        return view('livewire.students.dashboard')
            ->with([
                'student' => $student,
                'attendance' => $this->attendance,
                'fees' => $this->fees,
                'latestResult' => $this->latestResult,
                'subjectPerformance' => $this->subjectPerformance,
                'isGuardian' => $isGuardian,
                'studentId' => $studentIdForRoute,
            ])
            ->layout('layouts.student');
    }
}
