<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public ?int $studentId = null;

    public function mount($student = null): void
    {
        // If guardian opens /guardian/students/{student}/dashboard we receive {student} here.
        if ($student !== null) {
            $this->studentId = (int) $student;
        }

        // If a guardian hits "/" directly, send them to their children picker.
        $user = Auth::user();
        if ($user) {
            $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
            if ($roleName === 'Guardian' && $this->studentId === null) {
                redirect()->route('guardian.children')->send();
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
            }
        }

        return view('livewire.students.dashboard')
            ->with(['student' => $student])
            ->layout('layouts.student');
    }
}
