<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $class11Id = DB::table('classes')->where('name', '11')->value('id');
        $class12Id = DB::table('classes')->where('name', '12')->value('id');

        $scienceStreamId = DB::table('streams')->where('name', 'Science')->value('id');
        $commerceStreamId = DB::table('streams')->where('name', 'Commerce')->value('id');
        $artsStreamId = DB::table('streams')->where('name', 'Arts')->value('id');

        // Demo subjects (enough to support marks/attendance/results).
        $subjects = [
            // Class 11 - Science
            ['class_id' => $class11Id, 'stream_id' => $scienceStreamId, 'name' => 'Physics'],
            ['class_id' => $class11Id, 'stream_id' => $scienceStreamId, 'name' => 'Chemistry'],
            ['class_id' => $class11Id, 'stream_id' => $scienceStreamId, 'name' => 'Mathematics'],
            ['class_id' => $class11Id, 'stream_id' => $scienceStreamId, 'name' => 'English'],

            // Class 12 - Commerce
            ['class_id' => $class12Id, 'stream_id' => $commerceStreamId, 'name' => 'Accountancy'],
            ['class_id' => $class12Id, 'stream_id' => $commerceStreamId, 'name' => 'Business Studies'],
            ['class_id' => $class12Id, 'stream_id' => $commerceStreamId, 'name' => 'Economics'],
            ['class_id' => $class12Id, 'stream_id' => $commerceStreamId, 'name' => 'English'],

            // (Optional) Class 11 - Arts
            ['class_id' => $class11Id, 'stream_id' => $artsStreamId, 'name' => 'History'],
            ['class_id' => $class11Id, 'stream_id' => $artsStreamId, 'name' => 'Political Science'],
            ['class_id' => $class11Id, 'stream_id' => $artsStreamId, 'name' => 'English'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                [
                    'class_id' => $subject['class_id'],
                    'stream_id' => $subject['stream_id'],
                    'name' => $subject['name'],
                ],
                [
                    ...$subject,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

