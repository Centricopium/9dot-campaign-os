<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class HrPayrollPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['HR Manager', 'Payroll Manager', 'HR Executive'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

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

        $hrManager = array_values(array_diff($all, [
            'payroll.delete',
            'payroll.approve',
            'payroll.pay',
        ]));

        $payrollManager = [
            'hr_dashboard.view',
            'hr_employee.view',
            'hr_attendance.view',
            'hr_leave.view',
            'payroll.view',
            'payroll.create',
            'payroll.update',
            'payroll.delete',
            'payroll.process',
            'payroll.approve',
            'payroll.pay',
            'payroll.export',
        ];

        $hrExecutive = [
            'hr_dashboard.view',
            'hr_employee.view',
            'hr_employee.create',
            'hr_employee.update',
            'hr_attendance.view',
            'hr_attendance.create',
            'hr_attendance.update',
            'hr_leave.view',
            'hr_leave.create',
            'hr_leave.update',
        ];

        foreach (['Super Admin', 'Assembly Admin'] as $roleName) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->givePermissionTo($all);
        }

        foreach ([
            'HR Manager' => $hrManager,
            'Payroll Manager' => $payrollManager,
            'HR Executive' => $hrExecutive,
        ] as $roleName => $permissions) {
            Role::findByName($roleName)->syncPermissions($permissions);
        }

        foreach (['Campaign Manager', 'Data Manager', 'Survey Manager', 'Village Coordinator', 'Booth Coordinator', 'Booth Agent', 'Volunteer', 'Viewer'] as $roleName) {
            Role::query()->where('name', $roleName)->where('guard_name', 'web')->first()?->revokePermissionTo($all);
        }
    }
}
