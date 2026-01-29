<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Principal'],
            ['name' => 'Faculty'],
            ['name' => 'Guardian'],
        ];

        foreach ($roles as $role) {
            Role::updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
