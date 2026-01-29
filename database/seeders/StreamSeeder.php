<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StreamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $streams = [
            ['name' => 'Arts'],
            ['name' => 'Science'],
            ['name' => 'Commerce'],
        ];

        foreach ($streams as $stream) {
            DB::table('streams')->updateOrInsert(
                ['name' => $stream['name']],
                $stream
            );
        }
    }
}
