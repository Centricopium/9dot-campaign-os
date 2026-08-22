<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            'dashboard.view',

            /*
            |--------------------------------------------------------------------------
            | Constituency
            |--------------------------------------------------------------------------
            */
            'constituency.view',
            'constituency.create',
            'constituency.update',
            'constituency.delete',

            /*
            |--------------------------------------------------------------------------
            | Village
            |--------------------------------------------------------------------------
            */
            'village.view',
            'village.create',
            'village.update',
            'village.delete',

            /*
            |--------------------------------------------------------------------------
            | Booth
            |--------------------------------------------------------------------------
            */
            'booth.view',
            'booth.create',
            'booth.update',
            'booth.delete',

            /*
            |--------------------------------------------------------------------------
            | House
            |--------------------------------------------------------------------------
            */
            'house.view',
            'house.create',
            'house.update',
            'house.delete',

            /*
            |--------------------------------------------------------------------------
            | Voter
            |--------------------------------------------------------------------------
            */
            'voter.view',
            'voter.create',
            'voter.update',
            'voter.delete',

            /*
            |--------------------------------------------------------------------------
            | Survey
            |--------------------------------------------------------------------------
            */
            'survey.view',
            'survey.create',
            'survey.update',
            'survey.delete',

            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */
            'report.view',
            'report.export',

            /*
            |--------------------------------------------------------------------------
            | Candidate Selection
            |--------------------------------------------------------------------------
            */
            'candidate.view',
            'candidate.create',
            'candidate.update',
            'candidate.delete',
            'candidate.approve',
            'candidate_assessment.view',
            'candidate_assessment.create',
            'candidate_assessment.update',
            'candidate_assessment.delete',

            /*
            |--------------------------------------------------------------------------
            | Campaign Field Operations
            |--------------------------------------------------------------------------
            */
            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
            'campaign_task.delete',
            'campaign_task.assign',
            'campaign_task.review',
            'campaign_issue.view',
            'campaign_issue.create',
            'campaign_issue.update',
            'campaign_issue.delete',
            'campaign_issue.assign',
            'campaign_issue.resolve',
            'war_room.view',
            'war_room.manage',
            'war_room.review',
            'audit.view',
            'audit.export',
            'security.manage',
            'backup.view',
            'backup.create',
            'backup.restore',
            'campaign_event.view',
            'campaign_event.create',
            'campaign_event.update',
            'campaign_event.delete',
            'campaign_event.manage_team',
            'campaign_event.export',
            'campaign_finance.view',
            'campaign_finance.create',
            'campaign_finance.update',
            'campaign_finance.delete',
            'campaign_finance.approve',
            'campaign_communication.view',
            'campaign_communication.create',
            'campaign_communication.update',
            'campaign_communication.delete',
            'campaign_communication.approve',
            'campaign_communication.publish',
            'internal_message.view',
            'internal_message.send',
            'internal_message.broadcast',
            'internal_message.moderate',

            /*
            |--------------------------------------------------------------------------
            | Import
            |--------------------------------------------------------------------------
            */
            'import.village',
            'import.booth',
            'import.house',
            'import.voter',

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            /*
            |--------------------------------------------------------------------------
            | Security
            |--------------------------------------------------------------------------
            */
            'role.manage',
            'permission.manage',

        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
