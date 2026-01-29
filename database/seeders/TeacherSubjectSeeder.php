<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $teacherId = DB::table('teachers')->where('employee_code', 'EMP001')->value('id');
        if (! $teacherId) {
            return;
        }

        // Assign the teacher to all demo subjects for their respective classes.
        $subjects = DB::table('subjects')->get(['id', 'class_id']);

        foreach ($subjects as $subject) {
            DB::table('teacher_subjects')->updateOrInsert(
                [
                    'teacher_id' => $teacherId,
                    'subject_id' => $subject->id,
                    'class_id' => $subject->class_id,
                ],
                [
                    'teacher_id' => $teacherId,
                    'subject_id' => $subject->id,
                    'class_id' => $subject->class_id,
                ]
            );
        }
    }
}

