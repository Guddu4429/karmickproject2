<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkSeeder extends Seeder
{
    public function run(): void
    {
        $enteredBy = DB::table('users')->where('email', 'faculty@dpsrpk.edu')->value('id');
        $approvedBy = DB::table('users')->where('email', 'principal@dpsrpk.edu')->value('id');
        if (! $enteredBy) {
            return;
        }

        $academicYear = '2026';

        $student11 = DB::table('students')->where('admission_no', 'ADM2024001')->first();
        $student12 = DB::table('students')->where('admission_no', 'ADM2023001')->first();
        if (! $student11 || ! $student12) {
            return;
        }

        // Use two exams for demo marks (Unit Test + Half-Yearly)
        $exam11Unit = DB::table('exams')->where(['name' => 'Unit Test', 'academic_year' => $academicYear, 'class_id' => $student11->class_id])->value('id');
        $exam11Half = DB::table('exams')->where(['name' => 'Half-Yearly', 'academic_year' => $academicYear, 'class_id' => $student11->class_id])->value('id');

        $exam12Unit = DB::table('exams')->where(['name' => 'Unit Test', 'academic_year' => $academicYear, 'class_id' => $student12->class_id])->value('id');
        $exam12Half = DB::table('exams')->where(['name' => 'Half-Yearly', 'academic_year' => $academicYear, 'class_id' => $student12->class_id])->value('id');

        $subjects11 = DB::table('subjects')
            ->where('class_id', $student11->class_id)
            ->where('stream_id', $student11->stream_id)
            ->get(['id']);

        $subjects12 = DB::table('subjects')
            ->where('class_id', $student12->class_id)
            ->where('stream_id', $student12->stream_id)
            ->get(['id']);

        $seedMarks = function (int $studentId, int $examId, $subjects) use ($enteredBy, $approvedBy) {
            foreach ($subjects as $subject) {
                // deterministic-ish mark based on ids (so re-run stays same)
                $marks = 60 + (($studentId + $examId + $subject->id) % 36); // 60..95

                DB::table('marks')->updateOrInsert(
                    [
                        'student_id' => $studentId,
                        'exam_id' => $examId,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'student_id' => $studentId,
                        'exam_id' => $examId,
                        'subject_id' => $subject->id,
                        'marks_obtained' => $marks,
                        'entered_by' => $enteredBy,
                        'approved_by' => $approvedBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        };

        if ($exam11Unit) $seedMarks($student11->id, $exam11Unit, $subjects11);
        if ($exam11Half) $seedMarks($student11->id, $exam11Half, $subjects11);

        if ($exam12Unit) $seedMarks($student12->id, $exam12Unit, $subjects12);
        if ($exam12Half) $seedMarks($student12->id, $exam12Half, $subjects12);
    }
}

