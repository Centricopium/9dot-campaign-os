<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

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

            'constituency.view',

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

            'candidate.view',
            'candidate.create',
            'candidate.update',
            'candidate.delete',
            'candidate.approve',
            'candidate_assessment.view',
            'candidate_assessment.create',
            'candidate_assessment.update',
            'candidate_assessment.delete',

            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
            'campaign_task.delete',
            'campaign_task.assign',
            'campaign_task.review',

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

            'candidate.view',
            'candidate.update',
            'candidate_assessment.view',
            'candidate_assessment.create',
            'candidate_assessment.update',

            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
            'campaign_task.assign',
            'campaign_task.review',
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

            'campaign_task.view',
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

            'candidate.view',
            'candidate_assessment.view',

            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
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

            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
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

            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
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

            'campaign_task.view',
            'campaign_task.update',
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

            'campaign_task.view',
            'campaign_task.update',
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

            'campaign_task.view',
        ]);
    }
}
