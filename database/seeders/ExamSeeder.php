<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $class11Id = DB::table('classes')->where('name', '11')->value('id');
        $class12Id = DB::table('classes')->where('name', '12')->value('id');

        $academicYear = '2026';

        $exams = [
            ['name' => 'Unit Test', 'academic_year' => $academicYear, 'class_id' => $class11Id],
            ['name' => 'Half-Yearly', 'academic_year' => $academicYear, 'class_id' => $class11Id],
            ['name' => 'Annual', 'academic_year' => $academicYear, 'class_id' => $class11Id],

            ['name' => 'Unit Test', 'academic_year' => $academicYear, 'class_id' => $class12Id],
            ['name' => 'Half-Yearly', 'academic_year' => $academicYear, 'class_id' => $class12Id],
            ['name' => 'Annual', 'academic_year' => $academicYear, 'class_id' => $class12Id],
        ];

        foreach ($exams as $exam) {
            DB::table('exams')->updateOrInsert(
                [
                    'name' => $exam['name'],
                    'academic_year' => $exam['academic_year'],
                    'class_id' => $exam['class_id'],
                ],
                [
                    ...$exam,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

