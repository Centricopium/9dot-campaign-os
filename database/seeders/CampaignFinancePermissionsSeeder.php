<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CampaignFinancePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['campaign_finance.view', 'campaign_finance.create', 'campaign_finance.update', 'campaign_finance.delete', 'campaign_finance.approve'];

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ([
            'Super Admin' => $all,
            'Assembly Admin' => $all,
            'Campaign Manager' => array_values(array_diff($all, ['campaign_finance.delete'])),
        ] as $roleName => $permissions) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->givePermissionTo($permissions);
        }

        foreach (['Village Coordinator', 'Booth Coordinator', 'Volunteer', 'Viewer'] as $roleName) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->revokePermissionTo($all);
        }
    }
}
