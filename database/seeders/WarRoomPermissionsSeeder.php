<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class WarRoomPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['war_room.view', 'war_room.manage', 'war_room.review'];
        foreach ($all as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        } foreach (['Super Admin' => $all, 'Assembly Admin' => $all, 'Campaign Manager' => $all, 'Village Coordinator' => ['war_room.view', 'war_room.manage'], 'Booth Coordinator' => ['war_room.view', 'war_room.manage'], 'Viewer' => ['war_room.view']] as $name => $ps) {
            $r = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if ($r) {
                $r->givePermissionTo($ps);
            }
        }
    }
}
