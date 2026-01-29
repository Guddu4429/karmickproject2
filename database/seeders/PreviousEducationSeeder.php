<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreviousEducationSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('students')->get(['id', 'admission_no', 'first_name', 'last_name']);
        if ($students->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // One previous education record per student (idempotent by student_id + roll_number)
            $rollNumber = 'PREV-' . $student->admission_no;

            DB::table('previous_educations')->updateOrInsert(
                [
                    'student_id' => $student->id,
                    'roll_number' => $rollNumber,
                ],
                [
                    'student_id' => $student->id,
                    'board' => 'CBSE',
                    'school_name' => 'ABC Public School',
                    'roll_number' => $rollNumber,
                    'total_marks' => 500,
                    'percentage' => 90.00,
                    'rank' => null,
                    'passing_year' => 2024,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

