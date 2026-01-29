<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
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
        $principalRole = Role::where('name', 'Principal')->first();
        $facultyRole = Role::where('name', 'Faculty')->first();
        $guardianRole = Role::where('name', 'Guardian')->first();

        // 1. Create/Update Principal User
        $principal = User::updateOrCreate(
            ['email' => 'principal@dpsrpk.edu'],
            [
                'name' => 'Dr. John Smith',
                'phone' => '+91 98765 43210',
                'password' => Hash::make('password'),
                'role_id' => $principalRole->id,
            ]
        );

        // 2. Create/Update Faculty User
        $faculty = User::updateOrCreate(
            ['email' => 'faculty@dpsrpk.edu'],
            [
                'name' => 'Mrs. Sarah Johnson',
                'phone' => '+91 98765 43211',
                'password' => Hash::make('password'),
                'role_id' => $facultyRole->id,
            ]
        );

        // Create/Update Teacher record for faculty
        DB::table('teachers')->updateOrInsert(
            ['employee_code' => 'EMP001'],
            [
                'user_id' => $faculty->id,
                'designation' => 'Senior Teacher',
                'email' => 'faculty@dpsrpk.edu',
                'phone' => '+91 98765 43211',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Create/Update Guardian User
        $guardian = User::updateOrCreate(
            ['email' => 'guardian@example.com'],
            [
                'name' => 'Mr. Rajesh Kumar',
                'phone' => '+91 91234 56789',
                'password' => Hash::make('password'),
                'role_id' => $guardianRole->id,
            ]
        );

        // Create/Update Guardian record
        DB::table('guardians')->updateOrInsert(
            ['user_id' => $guardian->id],
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
        $guardianId = DB::table('guardians')->where('user_id', $guardian->id)->value('id');

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
