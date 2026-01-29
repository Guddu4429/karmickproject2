<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('students')->get(['id', 'class_id', 'stream_id']);
        if ($students->isEmpty()) {
            return;
        }

        // Create results for exams that already have marks.
        $marksByStudentExam = DB::table('marks')
            ->select('student_id', 'exam_id', DB::raw('SUM(marks_obtained) as total'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id', 'exam_id')
            ->get();

        foreach ($marksByStudentExam as $row) {
            $maxTotal = (int) $row->cnt * 100;
            $percentage = $maxTotal > 0 ? round(((int) $row->total / $maxTotal) * 100, 2) : 0.00;

            $grade = match (true) {
                $percentage >= 90 => 'A',
                $percentage >= 75 => 'B',
                $percentage >= 60 => 'C',
                $percentage >= 40 => 'D',
                default => 'F',
            };

            DB::table('results')->updateOrInsert(
                [
                    'student_id' => $row->student_id,
                    'exam_id' => $row->exam_id,
                ],
                [
                    'student_id' => $row->student_id,
                    'exam_id' => $row->exam_id,
                    'total_marks' => (int) $row->total,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'rank' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

