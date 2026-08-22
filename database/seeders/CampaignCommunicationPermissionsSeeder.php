<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CampaignCommunicationPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['campaign_communication.view', 'campaign_communication.create', 'campaign_communication.update', 'campaign_communication.delete', 'campaign_communication.approve', 'campaign_communication.publish'];

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ([
            'Super Admin' => $all,
            'Assembly Admin' => $all,
            'Campaign Manager' => array_values(array_diff($all, ['campaign_communication.delete'])),
            'Village Coordinator' => ['campaign_communication.view', 'campaign_communication.create', 'campaign_communication.update'],
            'Booth Coordinator' => ['campaign_communication.view'],
            'Volunteer' => ['campaign_communication.view'],
        ] as $roleName => $permissions) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->givePermissionTo($permissions);
        }
    }
}
