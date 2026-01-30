<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Teachers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Filters
    public $search = '';
    public $designation = '';

    // Add Teacher Modal
    public $showAddModal = false;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $employee_code = '';
    public $designation_input = '';
    public $password = '';
    public $password_confirmation = '';

    // Assign Classes Modal
    public $showAssignModal = false;
    public $selectedTeacherId = null;
    public $selectedStreamId = '';
    public $selectedClassId = '';
    public $selectedSubjectId = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedStatus = 'scheduled';

    // View Attendance Modal
    public $showAttendanceModal = false;
    public $attendanceTeacherId = null;
    public $attendanceRecords = [];

    // Reset page when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDesignation()
    {
        $this->resetPage();
    }


    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function resetAddForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->employee_code = '';
        $this->designation_input = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    // Generate Employee Code
    public function generateEmployeeCode()
    {
        // Get the last employee code
        $lastTeacher = Teacher::orderBy('id', 'desc')->first();
        
        if ($lastTeacher && $lastTeacher->employee_code) {
            // Extract number from last employee code (assuming format like EMP001, TCH001, etc.)
            preg_match('/\d+/', $lastTeacher->employee_code, $matches);
            $lastNumber = $matches ? (int)$matches[0] : 0;
            $newNumber = $lastNumber + 1;
            $this->employee_code = 'EMP' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            // First employee
            $this->employee_code = 'EMP0001';
        }
    }

    // Open Add Teacher Modal
    public function openAddModal()
    {
        $this->resetAddForm();
        $this->generateEmployeeCode();
        $this->showAddModal = true;
    }

    // Add Teacher
    public function addTeacher()
    {
        $this->validate([
            'employee_code' => 'required|string|unique:teachers,employee_code',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
            'designation_input' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'phone.regex' => 'Phone number must be exactly 10 digits.',
        ]);

        try {
            DB::beginTransaction();

            // Get Faculty role
            $facultyRole = Role::where('name', 'Faculty')->first();
            if (!$facultyRole) {
                session()->flash('error', 'Faculty role not found. Please create it first.');
                return;
            }

            // Create User
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role_id' => $facultyRole->id,
            ]);

            // Create Teacher
            Teacher::create([
                'user_id' => $user->id,
                'employee_code' => $this->employee_code,
                'designation' => $this->designation_input ?? '',
                'email' => $this->email,
                'phone' => $this->phone,
            ]);

            DB::commit();

            session()->flash('success', 'Teacher added successfully!');
            $this->closeAddModal();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error adding teacher: ' . $e->getMessage());
        }
    }

    // Open Assign Classes Modal
    public function openAssignModal($teacherId)
    {
        $this->selectedTeacherId = $teacherId;
        $this->selectedStreamId = '';
        $this->selectedClassId = '';
        $this->selectedSubjectId = '';
        $this->selectedDate = '';
        $this->selectedTime = '';
        $this->selectedStatus = 'scheduled';
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->selectedTeacherId = null;
        $this->selectedStreamId = '';
        $this->selectedClassId = '';
        $this->selectedSubjectId = '';
        $this->selectedDate = '';
        $this->selectedTime = '';
        $this->selectedStatus = 'scheduled';
    }

    // Reset subject when stream changes
    public function updatedSelectedStreamId()
    {
        $this->selectedSubjectId = '';
    }

    // Check if timing conflicts with existing assignments based on attendance logs
    public function checkTimingConflict($teacherId, $timing, $days)
    {
        if (empty($timing)) {
            return false; // No timing specified, so no conflict check needed
        }

        // Parse timing (format: "HH:MM-HH:MM")
        $timeParts = explode('-', $timing);
        if (count($timeParts) !== 2) {
            return false; // Invalid format, skip conflict check
        }

        $requestedStart = strtotime(trim($timeParts[0]));
        $requestedEnd = strtotime(trim($timeParts[1]));

        if ($requestedStart === false || $requestedEnd === false) {
            return false; // Invalid time format
        }

        // Get teacher's recent attendance logs to understand their typical working hours
        $recentLogs = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacherId)
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->where('status', 'Present')
            ->orderBy('attendance_date', 'desc')
            ->limit(10)
            ->get();

        if ($recentLogs->isEmpty()) {
            return false; // No attendance history, can't check conflicts
        }

        // Check if requested timing falls within teacher's typical working hours
        $suitableTimings = [];
        foreach ($recentLogs as $log) {
            $checkIn = strtotime($log->check_in_time);
            $checkOut = strtotime($log->check_out_time);
            
            // Check if requested time overlaps with teacher's working hours
            if ($requestedStart >= $checkIn && $requestedStart <= $checkOut) {
                // Requested start time is within working hours
                if ($requestedEnd <= $checkOut) {
                    // Requested end time is also within working hours - suitable timing
                    return false; // No conflict, timing is suitable
                }
            }
        }

        // Check for conflicts with existing class assignments
        $existingAssignments = DB::table('teacher_subjects')
            ->where('teacher_id', $teacherId)
            ->count();

        // If teacher has many assignments, warn about potential conflicts
        if ($existingAssignments > 5) {
            // Teacher has many assignments, might have timing conflicts
            // But we'll allow it and let admin decide
            return false;
        }

        // If we can't find suitable timing in recent logs, it might be a conflict
        // But for now, we'll allow it (you can make this stricter)
        return false;
    }

    // Assign Class/Subject to Teacher
    public function assignClass()
    {
        $this->validate([
            'selectedTeacherId' => 'required|exists:teachers,id',
            'selectedStreamId' => 'required|exists:streams,id',
            'selectedClassId' => 'required|exists:classes,id',
            'selectedSubjectId' => 'required|exists:subjects,id',
            'selectedDate' => 'required|date',
            'selectedTime' => 'required',
        ]);

        try {
            // Check if assignment already exists for the same date and time with stream
            $exists = DB::table('teacher_subjects')
                ->where('teacher_id', $this->selectedTeacherId)
                ->where('stream_id', $this->selectedStreamId)
                ->where('subject_id', $this->selectedSubjectId)
                ->where('date', $this->selectedDate)
                ->where('time', $this->selectedTime)
                ->exists();

            if ($exists) {
                session()->flash('error', 'This assignment already exists for the selected stream, date and time!');
                return;
            }

            // Create assignment (default status is 'scheduled')
            DB::table('teacher_subjects')->insert([
                'teacher_id' => $this->selectedTeacherId,
                'stream_id' => $this->selectedStreamId,
                'class_id' => $this->selectedClassId,
                'subject_id' => $this->selectedSubjectId,
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'status' => 'scheduled', // Default status when assigning
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            session()->flash('success', 'Class assigned successfully!');
            $this->closeAssignModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error assigning class: ' . $e->getMessage());
        }
    }

    // View Attendance
    public function viewAttendance($teacherId)
    {
        $this->attendanceTeacherId = $teacherId;
        $this->attendanceRecords = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacherId)
            ->orderBy('attendance_date', 'desc')
            ->limit(30)
            ->get();
        $this->showAttendanceModal = true;
    }

    public function closeAttendanceModal()
    {
        $this->showAttendanceModal = false;
        $this->attendanceTeacherId = null;
        $this->attendanceRecords = [];
    }

    // Delete Teacher (Soft delete - mark as inactive)
    public function deleteTeacher($id)
    {
        try {
            $teacher = Teacher::find($id);
            if ($teacher) {
                // You might want to soft delete the user as well
                // For now, we'll just delete the teacher record
                $teacher->delete();
                session()->flash('success', 'Teacher deleted successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting teacher: ' . $e->getMessage());
        }
    }


    // Get teacher assignments for display
    public function getTeacherAssignments($teacherId)
    {
        return DB::table('teacher_subjects')
            ->where('teacher_id', $teacherId)
            ->join('classes', 'teacher_subjects.class_id', '=', 'classes.id')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->select('classes.name as class_name', 'subjects.name as subject_name')
            ->get();
    }

    // Get today's attendance for a teacher
    public function getTodayAttendance($teacherId)
    {
        return DB::table('teacher_attendance_logs')
            ->where('teacher_id', $teacherId)
            ->whereDate('attendance_date', today())
            ->first();
    }

    public function render()
    {
        $teachers = Teacher::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('employee_code', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->designation, function ($q) {
                $q->where('designation', $this->designation);
            })
            ->orderBy('employee_code', 'asc')
            ->paginate(10);

        // Get unique designations for filter
        $designations = DB::table('teachers')
            ->distinct()
            ->pluck('designation')
            ->filter()
            ->values();

        // Get streams, classes and subjects for assignment modal
        $streams = DB::table('streams')->orderBy('name')->get();
        $classes = DB::table('classes')->orderBy('name')->get();
        
        // Get subjects filtered by stream_id if stream is selected
        $subjects = collect([]);
        if (!empty($this->selectedStreamId)) {
            $subjects = DB::table('subjects')
                ->where('stream_id', (int)$this->selectedStreamId)
                ->orderBy('name')
                ->get();
        }

        return view('livewire.teachers', [
            'teachers' => $teachers,
            'designations' => $designations,
            'streams' => $streams,
            'classes' => $classes,
            'subjects' => $subjects,
        ])->layout('layouts.student');
    }
}
