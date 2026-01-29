<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MarksheetController extends Controller
{
    public function download($resultId)
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        // Get the result with exam and student details
        $result = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->join('students', 'students.id', '=', 'results.student_id')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->where('results.id', $resultId)
            ->select([
                'results.*',
                'exams.name as exam_name',
                'exams.academic_year',
                'students.id as student_id',
                'students.first_name',
                'students.last_name',
                'students.admission_no',
                'students.roll_no',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->first();

        if (! $result) {
            abort(404, 'Marksheet not found.');
        }

        // Guardian ownership check
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName === 'Guardian') {
            $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');
            $studentGuardianId = DB::table('students')
                ->where('id', $result->student_id)
                ->value('guardian_id');

            if ($studentGuardianId != $guardianId) {
                abort(403, 'You are not allowed to download this marksheet.');
            }
        }

        // Get subject-wise marks for this exam
        $subjectMarks = DB::table('marks')
            ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
            ->where('marks.student_id', $result->student_id)
            ->where('marks.exam_id', $result->exam_id)
            ->select([
                'subjects.name as subject_name',
                'marks.marks_obtained',
            ])
            ->orderBy('subjects.name')
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.marksheet', [
            'result' => $result,
            'subjectMarks' => $subjectMarks,
        ]);

        $filename = 'Marksheet_' . $result->exam_name . '_' . $result->first_name . '_' . $result->last_name . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadLatest()
    {
        $user = Auth::user();
        if (! $user) {
            abort(401);
        }

        $studentId = session('active_student_id');
        if (! $studentId) {
            abort(404, 'No student selected.');
        }

        // Guardian ownership check
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
        if ($roleName === 'Guardian') {
            $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');
            $studentGuardianId = DB::table('students')
                ->where('id', $studentId)
                ->value('guardian_id');

            if ($studentGuardianId != $guardianId) {
                abort(403, 'You are not allowed to download this marksheet.');
            }
        }

        // Get latest result
        $result = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->join('students', 'students.id', '=', 'results.student_id')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->leftJoin('streams', 'streams.id', '=', 'students.stream_id')
            ->where('results.student_id', $studentId)
            ->orderByDesc('exams.academic_year')
            ->orderByDesc('results.id')
            ->select([
                'results.*',
                'exams.name as exam_name',
                'exams.academic_year',
                'students.id as student_id',
                'students.first_name',
                'students.last_name',
                'students.admission_no',
                'students.roll_no',
                'classes.name as class_name',
                'streams.name as stream_name',
            ])
            ->first();

        if (! $result) {
            abort(404, 'No marksheet available.');
        }

        // Get subject-wise marks
        $subjectMarks = DB::table('marks')
            ->join('subjects', 'subjects.id', '=', 'marks.subject_id')
            ->where('marks.student_id', $result->student_id)
            ->where('marks.exam_id', $result->exam_id)
            ->select([
                'subjects.name as subject_name',
                'marks.marks_obtained',
            ])
            ->orderBy('subjects.name')
            ->get();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.marksheet', [
            'result' => $result,
            'subjectMarks' => $subjectMarks,
        ]);

        $filename = 'Marksheet_' . $result->exam_name . '_' . $result->first_name . '_' . $result->last_name . '.pdf';

        return $pdf->download($filename);
    }
}
