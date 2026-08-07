<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findByName('Super Admin');

        $superAdmin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Assembly Admin
        |--------------------------------------------------------------------------
        */

        Role::findByName('Assembly Admin')->syncPermissions([

            'dashboard.view',

            'village.view',
            'village.create',
            'village.update',
            'village.delete',

            'booth.view',
            'booth.create',
            'booth.update',
            'booth.delete',

            'house.view',
            'house.create',
            'house.update',
            'house.delete',

            'voter.view',
            'voter.create',
            'voter.update',
            'voter.delete',

            'survey.view',
            'survey.create',
            'survey.update',
            'survey.delete',

            'report.view',
            'report.export',

            'import.village',
            'import.booth',
            'import.house',
            'import.voter',

            'user.view',
            'user.create',
            'user.update',
            'user.delete',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Campaign Manager
        |--------------------------------------------------------------------------
        */

        Role::findByName('Campaign Manager')->syncPermissions([

            'dashboard.view',

            'village.view',
            'booth.view',
            'house.view',
            'voter.view',

            'survey.view',

            'report.view',
            'report.export',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data Manager
        |--------------------------------------------------------------------------
        */

        Role::findByName('Data Manager')->syncPermissions([

            'dashboard.view',

            'house.view',
            'house.create',
            'house.update',

            'voter.view',
            'voter.create',
            'voter.update',

            'import.village',
            'import.booth',
            'import.house',
            'import.voter',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Survey Manager
        |--------------------------------------------------------------------------
        */

        Role::findByName('Survey Manager')->syncPermissions([

            'dashboard.view',

            'survey.view',
            'survey.create',
            'survey.update',
            'survey.delete',

            'voter.view',
            'house.view',

            'report.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Village Coordinator
        |--------------------------------------------------------------------------
        */

        Role::findByName('Village Coordinator')->syncPermissions([

            'dashboard.view',

            'village.view',

            'booth.view',

            'house.view',
            'house.update',

            'voter.view',
            'voter.update',

            'survey.view',
            'survey.create',
            'survey.update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Booth Coordinator
        |--------------------------------------------------------------------------
        */

        Role::findByName('Booth Coordinator')->syncPermissions([

            'dashboard.view',

            'booth.view',

            'house.view',
            'house.update',

            'voter.view',
            'voter.update',

            'survey.view',
            'survey.create',
            'survey.update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Booth Agent
        |--------------------------------------------------------------------------
        */

        Role::findByName('Booth Agent')->syncPermissions([

            'dashboard.view',

            'house.view',

            'voter.view',
            'voter.update',

            'survey.view',
            'survey.create',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Volunteer
        |--------------------------------------------------------------------------
        */

        Role::findByName('Volunteer')->syncPermissions([

            'house.view',

            'voter.view',

            'survey.view',
            'survey.create',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Viewer
        |--------------------------------------------------------------------------
        */

        Role::findByName('Viewer')->syncPermissions([

            'dashboard.view',

            'village.view',

            'booth.view',

            'house.view',

            'voter.view',

            'survey.view',

            'report.view',
        ]);
    }
}