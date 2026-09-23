<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

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

            'HR Manager',

            'Payroll Manager',

            'HR Executive',

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
