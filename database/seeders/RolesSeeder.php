<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [

            'Super Admin',

            'Assembly Admin',

            'Campaign Manager',

            'Data Manager',

            'Survey Manager',

            'Village Coordinator',

            'Booth Coordinator',

            'Booth Agent',

            'Volunteer',

            'Viewer',

        ];

        foreach ($roles as $role) {

            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}