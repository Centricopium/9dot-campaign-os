<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class InternalMessagePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = ['internal_message.view', 'internal_message.send', 'internal_message.broadcast', 'internal_message.moderate'];

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ([
            'Super Admin' => $all,
            'Assembly Admin' => $all,
            'Campaign Manager' => $all,
            'Village Coordinator' => ['internal_message.view', 'internal_message.send'],
            'Booth Coordinator' => ['internal_message.view', 'internal_message.send'],
            'Volunteer' => ['internal_message.view', 'internal_message.send'],
            'Viewer' => ['internal_message.view'],
        ] as $roleName => $permissions) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->givePermissionTo($permissions);
        }
    }
}
