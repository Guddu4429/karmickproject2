<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ClassSeeder::class,
            StreamSeeder::class,
            UserSeeder::class,
            SubjectSeeder::class,
            TeacherSubjectSeeder::class,
            ExamSeeder::class,
            MarkSeeder::class,
            ResultSeeder::class,
            AttendanceSeeder::class,
            FeePaymentSeeder::class,
            PreviousEducationSeeder::class,
            TeacherAttendanceLogSeeder::class,
        ]);
    }
}
