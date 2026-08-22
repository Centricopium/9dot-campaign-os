<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CampaignEventPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['campaign_event.view', 'campaign_event.create', 'campaign_event.update', 'campaign_event.delete', 'campaign_event.manage_team', 'campaign_event.export'];
        foreach ($all as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }foreach (['Super Admin' => $all, 'Assembly Admin' => $all, 'Campaign Manager' => array_values(array_diff($all, ['campaign_event.delete'])), 'Village Coordinator' => ['campaign_event.view', 'campaign_event.create', 'campaign_event.update', 'campaign_event.manage_team', 'campaign_event.export'], 'Booth Coordinator' => ['campaign_event.view', 'campaign_event.create', 'campaign_event.update'], 'Volunteer' => ['campaign_event.view']] as $name => $ps) {
            $r = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if ($r) {
                $r->givePermissionTo($ps);
            }
        }
    }
}
