<?php

namespace App\Livewire\Guardians;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Children extends Component
{
    public function render()
    {
        $user = Auth::user();

        $guardianId = null;
        $children = collect();

        if ($user) {
            $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');

            if ($guardianId) {
                $children = DB::table('students')
                    ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
                    ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
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
                    ->orderBy('classes.name')
                    ->orderBy('students.first_name')
                    ->get();
            }
        }

        return view('livewire.guardians.children', [
            'guardianId' => $guardianId,
            'children' => $children,
        ])->layout('layouts.guardian');
    }
}

