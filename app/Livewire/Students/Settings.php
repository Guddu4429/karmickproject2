<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Settings extends Component
{
    public $student = null;
    public $guardian = null;

    public $studentAddress = '';
    public $guardianName = '';
    public $guardianPhone = '';
    public $guardianEmail = '';
    public $guardianAddress = '';

    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

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
                'students.first_name',
                'students.last_name',
                'students.address',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->first();

        if (! $student) {
            abort(403, 'You are not allowed to view this student.');
        }

        $this->student = $student;

        $this->guardian = DB::table('guardians')
            ->where('id', $guardianId)
            ->select(['id', 'name', 'phone', 'email', 'address'])
            ->first();

        $this->studentAddress = $student->address ?? '';
        $this->guardianName = $this->guardian->name ?? '';
        $this->guardianPhone = $this->guardian->phone ?? '';
        $this->guardianEmail = $this->guardian->email ?? '';
        $this->guardianAddress = $this->guardian->address ?? '';
    }

    public function save(): void
    {
        if (! $this->student || ! $this->guardian) {
            return;
        }

        $this->validate([
            'studentAddress' => 'nullable|string|max:500',
            'guardianName' => 'required|string|max:255',
            'guardianPhone' => 'required|string|max:50',
            'guardianEmail' => 'required|email|max:255',
            'guardianAddress' => 'nullable|string|max:500',
        ]);

        DB::table('students')
            ->where('id', $this->student->id)
            ->update([
                'address' => $this->studentAddress,
                'updated_at' => now(),
            ]);

        DB::table('guardians')
            ->where('id', $this->guardian->id)
            ->update([
                'name' => $this->guardianName,
                'phone' => $this->guardianPhone,
                'email' => $this->guardianEmail,
                'address' => $this->guardianAddress,
                'updated_at' => now(),
            ]);

        session()->flash('success', 'Settings updated successfully.');
    }

    public function changePassword(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($this->new_password),
                'updated_at' => now(),
            ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.students.settings')
            ->layout('layouts.student');
    }
}
