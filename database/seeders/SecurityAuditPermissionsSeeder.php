<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SecurityAuditPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['audit.view', 'audit.export', 'security.manage'];
        foreach ($all as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }foreach (['Super Admin' => $all, 'Assembly Admin' => ['audit.view', 'audit.export'], 'Campaign Manager' => ['audit.view']] as $name => $ps) {
            $r = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if ($r) {
                $r->givePermissionTo($ps);
            }
        }
    }
}
