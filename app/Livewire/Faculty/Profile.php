<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Profile extends Component
{
    public $teacher;
    public $user;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount(): void
    {
        $user = Auth::user();
        $this->user = $user;
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset('current_password', 'new_password', 'new_password_confirmation');
        session()->flash('success', 'Password changed successfully.');
    }

    public function render()
    {
        return view('livewire.faculty.profile')
            ->layout('layouts.faculty');
    }
}
