<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class BackupPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['backup.view', 'backup.create', 'backup.restore'];
        foreach ($all as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }foreach (['Super Admin' => $all, 'Assembly Admin' => ['backup.view']] as $name => $ps) {
            $r = Role::query()->where('name', $name)->where('guard_name', 'web')->first();
            if ($r) {
                $r->givePermissionTo($ps);
            }
        }
    }
}
