<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $principalRoleId = DB::table('roles')->where('name', 'Principal')->value('id');
        $facultyRoleId = DB::table('roles')->where('name', 'Faculty')->value('id');
        $guardianRoleId = DB::table('roles')->where('name', 'Guardian')->value('id');

        if (! $principalRoleId || ! $facultyRoleId || ! $guardianRoleId) {
            return;
        }

        // 1. Create/Update Principal User
        DB::table('users')->updateOrInsert(
            ['email' => 'principal@dpsrpk.edu'],
            [
                'name' => 'Dr. John Smith',
                'email' => 'principal@dpsrpk.edu',
                'phone' => '+91 98765 43210',
                'password' => Hash::make('password'),
                'role_id' => $principalRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $principalId = DB::table('users')->where('email', 'principal@dpsrpk.edu')->value('id');

        // 2. Create/Update Faculty User
        DB::table('users')->updateOrInsert(
            ['email' => 'faculty@dpsrpk.edu'],
            [
                'name' => 'Mrs. Sarah Johnson',
                'email' => 'faculty@dpsrpk.edu',
                'phone' => '+91 98765 43211',
                'password' => Hash::make('password'),
                'role_id' => $facultyRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $facultyId = DB::table('users')->where('email', 'faculty@dpsrpk.edu')->value('id');

        // Create/Update Teacher record for faculty
        DB::table('teachers')->updateOrInsert(
            ['employee_code' => 'EMP001'],
            [
                'user_id' => $facultyId,
                'designation' => 'Senior Teacher',
                'email' => 'faculty@dpsrpk.edu',
                'phone' => '+91 98765 43211',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Create/Update Guardian User
        DB::table('users')->updateOrInsert(
            ['email' => 'guardian@example.com'],
            [
                'name' => 'Mr. Rajesh Kumar',
                'email' => 'guardian@example.com',
                'phone' => '+91 91234 56789',
                'password' => Hash::make('password'),
                'role_id' => $guardianRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $guardianUserId = DB::table('users')->where('email', 'guardian@example.com')->value('id');

        // Create/Update Guardian record
        DB::table('guardians')->updateOrInsert(
            ['user_id' => $guardianUserId],
            [
                'name' => 'Mr. Rajesh Kumar',
                'phone' => '+91 91234 56789',
                'email' => 'guardian@example.com',
                'address' => '123 Main Street, Kolkata, West Bengal',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Get guardian ID
        $guardianId = DB::table('guardians')->where('user_id', $guardianUserId)->value('id');

        // Get class and stream IDs
        $class11 = DB::table('classes')->where('name', '11')->first();
        $class12 = DB::table('classes')->where('name', '12')->first();
        $scienceStream = DB::table('streams')->where('name', 'Science')->first();
        $commerceStream = DB::table('streams')->where('name', 'Commerce')->first();

        // Create/Update Student 1 - Class 11, Science
        DB::table('students')->updateOrInsert(
            ['admission_no' => 'ADM2024001'],
            [
                'guardian_id' => $guardianId,
                'roll_no' => '11A001',
                'first_name' => 'Aarav',
                'last_name' => 'Kumar',
                'dob' => '2008-05-15',
                'gender' => 'Male',
                'address' => '123 Main Street, Kolkata, West Bengal',
                'class_id' => $class11->id,
                'stream_id' => $scienceStream->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create/Update Student 2 - Class 12, Commerce
        DB::table('students')->updateOrInsert(
            ['admission_no' => 'ADM2023001'],
            [
                'guardian_id' => $guardianId,
                'roll_no' => '12C001',
                'first_name' => 'Priya',
                'last_name' => 'Kumar',
                'dob' => '2007-08-20',
                'gender' => 'Female',
                'address' => '123 Main Street, Kolkata, West Bengal',
                'class_id' => $class12->id,
                'stream_id' => $commerceStream->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
