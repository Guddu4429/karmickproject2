<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $teacherId = DB::table('teachers')->where('employee_code', 'EMP001')->value('id');
        if (! $teacherId) {
            return;
        }

        $base = now()->startOfMonth();
        $days = [
            $base->copy()->addDays(1)->toDateString(),
            $base->copy()->addDays(2)->toDateString(),
            $base->copy()->addDays(3)->toDateString(),
            $base->copy()->addDays(4)->toDateString(),
            $base->copy()->addDays(5)->toDateString(),
        ];

        foreach ($days as $date) {
            DB::table('teacher_attendance_logs')->updateOrInsert(
                [
                    'teacher_id' => $teacherId,
                    'attendance_date' => $date,
                ],
                [
                    'teacher_id' => $teacherId,
                    'attendance_date' => $date,
                    'check_in_time' => '09:10:00',
                    'check_out_time' => '16:30:00',
                    'status' => 'Present',
                    'remarks' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

