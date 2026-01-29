<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Fees extends Component
{
    public $student = null;
    public array $summary = [];
    public array $payments = [];

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
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->first();

        if (! $student) {
            abort(403, 'You are not allowed to view this student.');
        }

        $this->student = $student;

        $payments = DB::table('fee_payments')
            ->where('student_id', $student->id)
            ->orderByDesc('payment_date')
            ->get();

        $this->payments = $payments->all();

        $totalPaid = (float) $payments->sum('amount');
        // Demo total fees (you can move this to config later)
        $annualTotal = 90000.0;
        $due = max(0, $annualTotal - $totalPaid);

        $this->summary = [
            'annual_total' => $annualTotal,
            'paid' => $totalPaid,
            'due' => $due,
        ];
    }

    public function render()
    {
        return view('livewire.students.fees')
            ->layout('layouts.student');
    }
}
