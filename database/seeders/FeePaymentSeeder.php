<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeePaymentSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('students')->get(['id', 'admission_no']);
        if ($students->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            $payments = [
                [
                    'receipt_no' => 'RCPT-' . $student->admission_no . '-01',
                    'amount' => 20000.00,
                    'payment_date' => now()->subDays(20)->toDateString(),
                    'mode' => 'Online',
                ],
                [
                    'receipt_no' => 'RCPT-' . $student->admission_no . '-02',
                    'amount' => 19000.00,
                    'payment_date' => now()->subDays(60)->toDateString(),
                    'mode' => 'Cash',
                ],
                [
                    'receipt_no' => 'RCPT-' . $student->admission_no . '-03',
                    'amount' => 40000.00,
                    'payment_date' => now()->subDays(180)->toDateString(),
                    'mode' => 'Cheque',
                ],
            ];

            foreach ($payments as $p) {
                DB::table('fee_payments')->updateOrInsert(
                    ['receipt_no' => $p['receipt_no']],
                    [
                        'student_id' => $student->id,
                        'amount' => $p['amount'],
                        'payment_date' => $p['payment_date'],
                        'mode' => $p['mode'],
                        'receipt_no' => $p['receipt_no'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

