<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $teacherId = DB::table('teachers')->where('employee_code', 'EMP001')->value('id');
        if (! $teacherId) {
            return;
        }

        $students = DB::table('students')->get(['id', 'class_id', 'stream_id']);
        if ($students->isEmpty()) {
            return;
        }

        // Seed 5 days of attendance per student per subject (current month).
        $base = now()->startOfMonth();
        $dates = [
            $base->copy()->addDays(1)->toDateString(),
            $base->copy()->addDays(2)->toDateString(),
            $base->copy()->addDays(3)->toDateString(),
            $base->copy()->addDays(4)->toDateString(),
            $base->copy()->addDays(5)->toDateString(),
        ];

        foreach ($students as $student) {
            $subjects = DB::table('subjects')
                ->where('class_id', $student->class_id)
                ->where('stream_id', $student->stream_id)
                ->get(['id']);

            foreach ($subjects as $subject) {
                foreach ($dates as $date) {
                    $status = ((($student->id + $subject->id + (int) str_replace('-', '', $date)) % 10) < 8)
                        ? 'Present'
                        : 'Absent';

                    DB::table('attendance')->updateOrInsert(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                        ],
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'date' => $date,
                            'status' => $status,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}

