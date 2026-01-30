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

    // Teacher-specific properties
    public ?object $teacher = null;
    public int $classesAssigned = 0;
    public array $subjectsHandled = [];
    public int $attendancePending = 0;
    public string $upcomingExam = 'N/A';
    public array $todaySchedule = [];
    public array $attendanceAlerts = [];
    public array $adminNotices = [];
    public ?object $checkInStatus = null;

    // Admin/Principal-specific properties
    public int $totalStudents = 0;
    public int $totalTeachers = 0;
    public int $newAdmissions = 0;
    public string $upcomingExams = 'N/A';
    public array $enquiries = [];
    public ?object $selectedEnquiry = null;

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

    public function checkIn(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName !== 'Faculty') {
            return;
        }

        $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (!$teacher) {
            return;
        }

        $today = now()->toDateString();
        $currentTime = now()->toTimeString();

        // Check if already checked in today
        $existingLog = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existingLog && $existingLog->check_in_time) {
            // Already checked in
            return;
        }

        if ($existingLog) {
            // Update existing record
            DB::table('teacher_attendance_logs')
                ->where('id', $existingLog->id)
                ->update([
                    'check_in_time' => $currentTime,
                    'status' => 'Present',
                    'updated_at' => now(),
                ]);
        } else {
            // Create new record
            DB::table('teacher_attendance_logs')->insert([
                'teacher_id' => $teacher->id,
                'attendance_date' => $today,
                'check_in_time' => $currentTime,
                'status' => 'Present',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Refresh check-in status
        $this->checkInStatus = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->first();

        // Dispatch event to show toast
        $this->dispatch('checkInSuccess');
    }

    public function checkOut(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName !== 'Faculty') {
            return;
        }

        $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (!$teacher) {
            return;
        }

        $today = now()->toDateString();
        $currentTime = now()->toTimeString();

        // Update check-out time
        DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->update([
                'check_out_time' => $currentTime,
                'updated_at' => now(),
            ]);

        // Refresh check-in status
        $this->checkInStatus = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacher->id)
            ->where('attendance_date', $today)
            ->first();
    }

    public function toggleEnquiryRead($enquiryId): void
    {
        $enquiry = DB::table('enquiries')->where('id', $enquiryId)->first();
        if ($enquiry) {
            DB::table('enquiries')
                ->where('id', $enquiryId)
                ->update(['is_read' => !$enquiry->is_read]);
            
            // Refresh enquiries list
            $this->loadEnquiries();
        }
    }

    public function viewEnquiry($enquiryId): void
    {
        $this->selectedEnquiry = DB::table('enquiries')->where('id', $enquiryId)->first();
        
        // Mark as read when viewing
        if ($this->selectedEnquiry && !$this->selectedEnquiry->is_read) {
            DB::table('enquiries')
                ->where('id', $enquiryId)
                ->update(['is_read' => true]);
            $this->selectedEnquiry->is_read = true;
        }
    }

    public function closeEnquiryView(): void
    {
        $this->selectedEnquiry = null;
    }

    private function loadEnquiries(): void
    {
        $this->enquiries = DB::table('enquiries')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($enquiry, $index) {
                return [
                    'id' => $enquiry->id,
                    'sl_no' => $index + 1,
                    'email_from' => $enquiry->email_from,
                    'subject' => $enquiry->subject,
                    'is_read' => (bool) $enquiry->is_read,
                    'created_at' => $enquiry->created_at,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        $user = Auth::user();
        $roleName = $user ? DB::table('roles')->where('id', $user->role_id)->value('name') : null;
        $isTeacher = $roleName === 'Faculty';
        $isPrincipal = $roleName === 'Principal';
        $isGuardian = $roleName === 'Guardian';
        $isStudent = $roleName === 'Student';

        $student = null;

        // Load admin/principal data if principal is logged in
        if ($isPrincipal) {
            // Total Students
            $this->totalStudents = DB::table('students')->count();

            // Total Teachers
            $this->totalTeachers = DB::table('teachers')->count();

            // New Admissions (students created in last 30 days)
            $this->newAdmissions = DB::table('students')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            // Upcoming Exams
            $upcomingExam = DB::table('exams')
                ->where('academic_year', now()->format('Y'))
                ->orderBy('created_at')
                ->first();
            $this->upcomingExams = $upcomingExam ? $upcomingExam->name : 'N/A';

            // Load enquiries
            $this->loadEnquiries();

            // Get attendance alerts (mock for now)
            $this->attendanceAlerts = [
                'Class IX-B attendance pending',
                'Attendance cutoff alert for Class X-A',
            ];

            // Get admin notices (mock for now)
            $this->adminNotices = [
                'Half-Yearly exams start from 15th September',
                'Marks submission deadline: 10th September',
            ];
        }

        // Load teacher data if teacher is logged in (not principal)
        if ($isTeacher) {
            $teacher = DB::table('teachers')
                ->where('user_id', $user->id)
                ->first();

            if ($teacher) {
                $this->teacher = $teacher;

                // Get classes assigned count (unique classes from teacher_subjects)
                $this->classesAssigned = DB::table('teacher_subjects')
                    ->where('teacher_id', $teacher->id)
                    ->distinct('class_id')
                    ->count('class_id');

                // Get subjects handled (array of subject names)
                $subjects = DB::table('teacher_subjects')
                    ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                    ->where('teacher_subjects.teacher_id', $teacher->id)
                    ->distinct('subjects.name')
                    ->pluck('subjects.name')
                    ->toArray();
                $this->subjectsHandled = !empty($subjects) ? array_unique($subjects) : [];

                // Get attendance pending count (students in teacher's classes without attendance for today)
                $today = now()->toDateString();
                
                // Get class IDs and subject IDs that this teacher teaches
                $teacherAssignments = DB::table('teacher_subjects')
                    ->where('teacher_id', $teacher->id)
                    ->get(['class_id', 'subject_id']);
                
                $pendingCount = 0;
                foreach ($teacherAssignments as $assignment) {
                    // Count students in this class/stream combination
                    $students = DB::table('students')
                        ->where('class_id', $assignment->class_id)
                        ->get(['id', 'stream_id']);
                    
                    foreach ($students as $student) {
                        // Check if attendance exists for this student, subject, and date
                        $attendanceExists = DB::table('attendance')
                            ->where('student_id', $student->id)
                            ->where('subject_id', $assignment->subject_id)
                            ->where('date', $today)
                            ->exists();
                        
                        if (!$attendanceExists) {
                            $pendingCount++;
                        }
                    }
                }
                
                $this->attendancePending = $pendingCount;

                // Get upcoming exam (next exam by date for classes this teacher teaches)
                $teacherClassIds = DB::table('teacher_subjects')
                    ->where('teacher_id', $teacher->id)
                    ->distinct()
                    ->pluck('class_id')
                    ->toArray();

                $upcomingExam = DB::table('exams')
                    ->whereIn('class_id', $teacherClassIds)
                    ->where('academic_year', now()->format('Y'))
                    ->orderBy('name')
                    ->first();
                $this->upcomingExam = $upcomingExam ? $upcomingExam->name : 'N/A';

                // Get today's teaching schedule (mock data for now - would need a schedule table)
                // For now, show classes and subjects assigned
                $scheduleData = DB::table('teacher_subjects')
                    ->join('subjects', 'subjects.id', '=', 'teacher_subjects.subject_id')
                    ->join('classes', 'classes.id', '=', 'teacher_subjects.class_id')
                    ->leftJoin('streams', 'streams.id', '=', 'subjects.stream_id')
                    ->where('teacher_subjects.teacher_id', $teacher->id)
                    ->select([
                        'classes.name as class_name',
                        'streams.name as section_name',
                        'subjects.name as subject_name',
                    ])
                    ->get()
                    ->map(function ($item) {
                        return [
                            'class' => $item->class_name,
                            'section' => $item->section_name ?? '-',
                            'subject' => $item->subject_name,
                            'time' => '09:00 – 09:45', // Mock time - would come from schedule table
                            'status' => now()->format('H:i') >= '09:00' && now()->format('H:i') <= '09:45' ? 'Completed' : 'Pending',
                        ];
                    })
                    ->toArray();
                $this->todaySchedule = $scheduleData;

                // Get attendance alerts (mock for now)
                $this->attendanceAlerts = [
                    'Class IX-B attendance pending',
                    'Attendance cutoff alert for Class X-A',
                ];

                // Get admin notices (mock for now)
                $this->adminNotices = [
                    'Half-Yearly exams start from 15th September',
                    'Marks submission deadline: 10th September',
                ];

                // Get check-in status for today
                $this->checkInStatus = DB::table('teacher_attendance_logs')
                    ->where('teacher_id', $teacher->id)
                    ->where('attendance_date', $today)
                    ->first();
            }
        }

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
                // Show highest marks from any exam of each type (to show teacher-set marks)
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
                'isTeacher' => $isTeacher,
                'isPrincipal' => $isPrincipal,
                'isGuardian' => $isGuardian,
                'isStudent' => $isStudent,
                'teacher' => $this->teacher,
                'classesAssigned' => $this->classesAssigned,
                'subjectsHandled' => $this->subjectsHandled,
                'attendancePending' => $this->attendancePending,
                'upcomingExam' => $this->upcomingExam,
                'todaySchedule' => $this->todaySchedule,
                'attendanceAlerts' => $this->attendanceAlerts,
                'adminNotices' => $this->adminNotices,
                'checkInStatus' => $this->checkInStatus,
                'totalStudents' => $this->totalStudents,
                'totalTeachers' => $this->totalTeachers,
                'newAdmissions' => $this->newAdmissions,
                'upcomingExams' => $this->upcomingExams,
                'enquiries' => $this->enquiries,
                'selectedEnquiry' => $this->selectedEnquiry,
            ])
            ->layout('layouts.student');
    }
}
