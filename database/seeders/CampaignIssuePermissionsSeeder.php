<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CampaignIssuePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['campaign_issue.view', 'campaign_issue.create', 'campaign_issue.update', 'campaign_issue.delete', 'campaign_issue.assign', 'campaign_issue.resolve'];
        foreach ($all as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $map = ['Super Admin' => $all, 'Assembly Admin' => $all, 'Campaign Manager' => array_values(array_diff($all, ['campaign_issue.delete'])), 'Survey Manager' => ['campaign_issue.view', 'campaign_issue.create', 'campaign_issue.update'], 'Village Coordinator' => ['campaign_issue.view', 'campaign_issue.create', 'campaign_issue.update'], 'Booth Coordinator' => ['campaign_issue.view', 'campaign_issue.create', 'campaign_issue.update'], 'Booth Agent' => ['campaign_issue.view', 'campaign_issue.create', 'campaign_issue.update'], 'Volunteer' => ['campaign_issue.view', 'campaign_issue.create'], 'Viewer' => ['campaign_issue.view']];
        foreach ($map as $name => $permissions) {
            $role = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }
}
