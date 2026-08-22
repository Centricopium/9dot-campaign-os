<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Role Exists
        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        // Create or Update Super Admin
        $user = User::updateOrCreate(

            [
                'email' => 'admin@9dotcampaign.com',
            ],

            [
                'name' => 'Super Administrator',

                'password' => Hash::make('admin123'),

                'mobile' => null,

                'designation' => 'System Administrator',

                'employee_code' => 'SA-001',

                'is_super_admin' => true,

                'is_active' => true,
            ]
        );

        // Assign Role
        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }
    }
}
