<?php

namespace App\Livewire\Faculty;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckInOut extends Component
{
    public $teacher;
    public ?object $todayLog = null;
    public array $attendanceHistory = [];
    public $totalWorkingHours = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = DB::table('teachers')->where('user_id', $user->id)->first();
        if (! $this->teacher) {
            return;
        }

        $this->todayLog = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->where('attendance_date', now()->toDateString())
            ->first();

        $this->attendanceHistory = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->orderByDesc('attendance_date')
            ->limit(30)
            ->get()
            ->all();

        $this->calculateWorkingHours();
    }

    protected function calculateWorkingHours(): void
    {
        $logs = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->get();

        $total = 0;
        foreach ($logs as $log) {
            $in = \Carbon\Carbon::parse($log->attendance_date.' '.$log->check_in_time, config('app.timezone'));
            $out = \Carbon\Carbon::parse($log->attendance_date.' '.$log->check_out_time, config('app.timezone'));
            $total += $out->diffInMinutes($in);
        }
        $this->totalWorkingHours = round($total / 60, 1);
    }

    public function checkIn(): void
    {
        DB::table('teacher_attendance_logs')->updateOrInsert(
            [
                'teacher_id' => $this->teacher->id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'check_in_time' => now()->format('H:i:s'),
                'status' => 'Present',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->todayLog = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->where('attendance_date', now()->toDateString())
            ->first();
        $this->mount();
    }

    public function checkOut(): void
    {
        DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->where('attendance_date', now()->toDateString())
            ->update([
                'check_out_time' => now()->format('H:i:s'),
                'updated_at' => now(),
            ]);
        $this->todayLog = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->where('attendance_date', now()->toDateString())
            ->first();
        $this->calculateWorkingHours();
        $this->attendanceHistory = DB::table('teacher_attendance_logs')
            ->where('teacher_id', $this->teacher->id)
            ->orderByDesc('attendance_date')
            ->limit(30)
            ->get()
            ->all();
    }

    public function render()
    {
        return view('livewire.faculty.check-in-out')
            ->layout('layouts.faculty');
    }
}
