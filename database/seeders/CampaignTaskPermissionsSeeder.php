<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CampaignTaskPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'campaign_task.view',
            'campaign_task.create',
            'campaign_task.update',
            'campaign_task.delete',
            'campaign_task.assign',
            'campaign_task.review',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $assignments = [
            'Super Admin' => $permissions,
            'Assembly Admin' => $permissions,
            'Campaign Manager' => array_values(array_diff($permissions, ['campaign_task.delete'])),
            'Data Manager' => ['campaign_task.view'],
            'Survey Manager' => ['campaign_task.view', 'campaign_task.create', 'campaign_task.update'],
            'Village Coordinator' => ['campaign_task.view', 'campaign_task.create', 'campaign_task.update'],
            'Booth Coordinator' => ['campaign_task.view', 'campaign_task.create', 'campaign_task.update'],
            'Booth Agent' => ['campaign_task.view', 'campaign_task.update'],
            'Volunteer' => ['campaign_task.view', 'campaign_task.update'],
            'Viewer' => ['campaign_task.view'],
        ];

        foreach ($assignments as $roleName => $rolePermissions) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role) {
                $role->givePermissionTo($rolePermissions);
            }
        }
    }
}
