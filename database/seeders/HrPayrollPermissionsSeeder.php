<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class HrPayrollPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = [
            'hr_dashboard.view',
            'hr_employee.view',
            'hr_employee.create',
            'hr_employee.update',
            'hr_employee.delete',
            'hr_attendance.view',
            'hr_attendance.create',
            'hr_attendance.update',
            'hr_attendance.delete',
            'hr_leave.view',
            'hr_leave.create',
            'hr_leave.update',
            'hr_leave.delete',
            'hr_leave.approve',
            'payroll.view',
            'payroll.create',
            'payroll.update',
            'payroll.delete',
            'payroll.process',
            'payroll.approve',
            'payroll.pay',
            'payroll.export',
        ];

        foreach ($all as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $campaignManager = array_values(array_diff($all, [
            'hr_employee.delete',
            'hr_attendance.delete',
            'hr_leave.delete',
            'payroll.delete',
            'payroll.approve',
            'payroll.pay',
        ]));

        foreach ([
            'Super Admin' => $all,
            'Assembly Admin' => $all,
            'Campaign Manager' => $campaignManager,
        ] as $roleName => $permissions) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->givePermissionTo($permissions);
        }

        foreach (['Data Manager', 'Survey Manager', 'Village Coordinator', 'Booth Coordinator', 'Booth Agent', 'Volunteer', 'Viewer'] as $roleName) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->revokePermissionTo($all);
        }
    }
}
