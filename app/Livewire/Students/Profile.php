<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Profile extends Component
{
    use WithFileUploads;

    public $student = null;
    public $guardian = null;
    public array $previousEducations = [];
    public $photo = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');

        // For guardians, load active student from session
        if ($roleName === 'Guardian') {
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
                    'students.admission_no',
                    'students.roll_no',
                    'students.dob',
                    'students.gender',
                    'students.address',
                    'students.profile_photo_path',
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
                ->select(['name', 'phone', 'email', 'address'])
                ->first();

            $this->previousEducations = DB::table('previous_educations')
                ->where('student_id', $student->id)
                ->orderByDesc('passing_year')
                ->get()
                ->all();
        }
    }

    public function updatePhoto()
    {
        if (! $this->student || ! $this->photo) {
            return;
        }

        $this->validate([
            'photo' => 'image|max:2048', // 2MB
        ]);

        // Delete old photo if exists
        if ($this->student->profile_photo_path) {
            \Storage::disk('public')->delete($this->student->profile_photo_path);
        }

        // Store new photo
        $path = $this->photo->store('profile-photos', 'public');

        DB::table('students')
            ->where('id', $this->student->id)
            ->update([
                'profile_photo_path' => $path,
            ]);

        $this->student->profile_photo_path = $path;
        $this->photo = null;

        session()->flash('success', 'Profile picture updated successfully.');
    }

    public function deletePhoto()
    {
        if (! $this->student || ! $this->student->profile_photo_path) {
            return;
        }

        \Storage::disk('public')->delete($this->student->profile_photo_path);

        DB::table('students')
            ->where('id', $this->student->id)
            ->update([
                'profile_photo_path' => null,
            ]);

        $this->student->profile_photo_path = null;

        session()->flash('success', 'Profile picture removed.');
    }


    public function render()
    {
        return view('livewire.students.profile')
            ->layout('layouts.student');
    }
}
