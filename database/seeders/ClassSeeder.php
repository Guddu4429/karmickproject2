<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['name' => '11'],
            ['name' => '12'],
        ];

        foreach ($classes as $class) {
            DB::table('classes')->updateOrInsert(
                ['name' => $class['name']],
                $class
            );
        }
    }
}
