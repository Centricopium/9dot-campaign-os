<?php

namespace Tests\Feature;

use App\Models\Constituency;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\PayrollRun;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Payroll\PayrollCalculationService;
use Database\Seeders\HrPayrollPermissionsSeeder;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class HrPayrollModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_payroll_is_calculated_from_employee_salary_structure(): void
    {
        $constituency = Constituency::create(['name' => 'Vansda', 'state' => 'Gujarat']);
        $employee = Employee::create([
            'constituency_id' => $constituency->id,
            'employee_code' => 'EMP-001',
            'name' => 'Field Manager',
            'employment_type' => 'Full Time',
            'salary_type' => 'Monthly',
            'base_salary' => 30000,
            'default_allowance' => 2000,
            'default_deduction' => 500,
            'joining_date' => '2026-01-01',
            'status' => 'Active',
        ]);
        $run = PayrollRun::create([
            'constituency_id' => $constituency->id,
            'period_year' => 2026,
            'period_month' => 9,
            'period_start' => '2026-09-01',
            'period_end' => '2026-09-30',
        ]);

        app(PayrollCalculationService::class)->process($run);

        $item = $run->fresh()->items()->where('employee_id', $employee->id)->firstOrFail();
        $this->assertSame('Processed', $run->fresh()->status);
        $this->assertSame('30000.00', $item->basic_pay);
        $this->assertSame('32000.00', $item->gross_pay);
        $this->assertSame('31500.00', $item->net_pay);
        $this->assertSame($run->fresh()->working_days.'.00', $item->present_days);
    }

    public function test_sensitive_employee_payment_fields_are_encrypted_at_rest(): void
    {
        $constituency = Constituency::create(['name' => 'Dang', 'state' => 'Gujarat']);
        $employee = Employee::create([
            'constituency_id' => $constituency->id,
            'employee_code' => 'EMP-SECURE',
            'name' => 'Payroll Employee',
            'joining_date' => today(),
            'identity_number' => '1234-5678-9012',
            'bank_account_number' => '987654321001',
            'upi_id' => 'employee@upi',
        ]);

        $raw = DB::table('employees')->where('id', $employee->id)->first();

        $this->assertNotSame('1234-5678-9012', $raw->identity_number);
        $this->assertNotSame('987654321001', $raw->bank_account_number);
        $this->assertNotSame('employee@upi', $raw->upi_id);
        $this->assertSame('987654321001', $employee->fresh()->bank_account_number);
        $this->assertSame('••••••••1001', $employee->fresh()->masked_bank_account);
    }

    public function test_unpaid_leave_reduces_monthly_salary_without_requiring_daily_attendance(): void
    {
        $constituency = Constituency::create(['name' => 'Unpaid Leave AC', 'state' => 'Gujarat']);
        $employee = Employee::create([
            'constituency_id' => $constituency->id,
            'employee_code' => 'EMP-LEAVE',
            'name' => 'Leave Employee',
            'salary_type' => 'Monthly',
            'base_salary' => 30000,
            'joining_date' => '2026-01-01',
        ]);
        EmployeeLeave::create([
            'employee_id' => $employee->id,
            'leave_type' => 'Unpaid',
            'starts_on' => '2026-09-07',
            'ends_on' => '2026-09-07',
            'status' => 'Approved',
        ]);
        $run = PayrollRun::create([
            'constituency_id' => $constituency->id,
            'period_year' => 2026,
            'period_month' => 9,
            'period_start' => '2026-09-01',
            'period_end' => '2026-09-30',
        ]);

        app(PayrollCalculationService::class)->process($run);

        $item = $run->items()->firstOrFail();
        $expected = round(30000 / $run->fresh()->working_days * ($run->fresh()->working_days - 1), 2);
        $this->assertSame('1.00', $item->unpaid_leave_days);
        $this->assertSame(number_format($expected, 2, '.', ''), $item->basic_pay);
        $this->assertSame(number_format($expected, 2, '.', ''), $item->net_pay);
    }

    public function test_assembly_admin_only_sees_assigned_constituency_hr_records(): void
    {
        $first = Constituency::create(['name' => 'Vansda', 'state' => 'Gujarat']);
        $second = Constituency::create(['name' => 'Dang', 'state' => 'Gujarat']);
        Employee::create(['constituency_id' => $first->id, 'employee_code' => 'EMP-A', 'name' => 'Visible Employee', 'joining_date' => today()]);
        Employee::create(['constituency_id' => $second->id, 'employee_code' => 'EMP-B', 'name' => 'Hidden Employee', 'joining_date' => today()]);

        Role::create(['name' => 'Assembly Admin', 'guard_name' => 'web']);
        $user = User::factory()->create(['constituency_id' => $first->id, 'is_active' => true]);
        $user->assignRole('Assembly Admin');
        $this->actingAs($user);

        $this->assertSame(['Visible Employee'], Employee::query()->pluck('name')->all());
        $this->assertSame(['Vansda'], Constituency::query()->pluck('name')->all());
    }

    public function test_authorized_user_can_open_hr_dashboard_and_export_payslip(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'hr_dashboard.view',
            'hr_employee.view',
            'hr_employee.create',
            'hr_attendance.view',
            'hr_attendance.create',
            'hr_leave.view',
            'hr_leave.create',
            'payroll.view',
            'payroll.create',
            'payroll.export',
        ];
        foreach ($permissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo($permissions);
        $constituency = Constituency::create(['name' => 'Vansda', 'state' => 'Gujarat']);
        $employee = Employee::create(['constituency_id' => $constituency->id, 'employee_code' => 'EMP-PDF', 'name' => 'PDF Employee', 'salary_type' => 'Monthly', 'base_salary' => 25000, 'joining_date' => '2026-01-01']);
        $run = PayrollRun::create(['constituency_id' => $constituency->id, 'period_year' => 2026, 'period_month' => 9, 'period_start' => '2026-09-01', 'period_end' => '2026-09-30']);
        app(PayrollCalculationService::class)->process($run);
        $item = $run->items()->where('employee_id', $employee->id)->firstOrFail();

        $this->actingAs($user)->get('/admin/hr-payroll-centre')->assertOk()->assertSeeText('HR & Payroll Command Centre');
        $this->actingAs($user)->get('/admin/employees')->assertOk();
        $this->actingAs($user)->get('/admin/employees/create')->assertOk();
        $this->actingAs($user)->get('/admin/employee-attendances')->assertOk();
        $this->actingAs($user)->get('/admin/employee-leaves')->assertOk();
        $this->actingAs($user)->get('/admin/payroll-runs')->assertOk();
        $this->actingAs($user)->get('/admin/payroll-runs/create')->assertOk();
        $this->actingAs($user)->get(route('hr.payroll.payslip', $item))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->actingAs($user)->get(route('hr.payroll.register', $run))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_dedicated_hr_roles_receive_separated_permissions_and_constituency_scope(): void
    {
        $this->seed([RolesSeeder::class, PermissionsSeeder::class, HrPayrollPermissionsSeeder::class]);

        $hrManager = Role::findByName('HR Manager');
        $payrollManager = Role::findByName('Payroll Manager');
        $hrExecutive = Role::findByName('HR Executive');

        $this->assertTrue($hrManager->hasPermissionTo('hr_leave.approve'));
        $this->assertTrue($hrManager->hasPermissionTo('payroll.process'));
        $this->assertFalse($hrManager->hasPermissionTo('payroll.pay'));
        $this->assertTrue($payrollManager->hasPermissionTo('payroll.approve'));
        $this->assertTrue($payrollManager->hasPermissionTo('payroll.pay'));
        $this->assertFalse($payrollManager->hasPermissionTo('hr_employee.update'));
        $this->assertTrue($hrExecutive->hasPermissionTo('hr_attendance.create'));
        $this->assertFalse($hrExecutive->hasPermissionTo('hr_leave.approve'));
        $this->assertFalse($hrExecutive->hasPermissionTo('payroll.view'));

        $first = Constituency::create(['name' => 'Scoped HR AC', 'state' => 'Gujarat']);
        $second = Constituency::create(['name' => 'Outside HR AC', 'state' => 'Gujarat']);
        Employee::create(['constituency_id' => $first->id, 'employee_code' => 'EMP-HR-1', 'name' => 'Scoped Employee', 'joining_date' => today()]);
        Employee::create(['constituency_id' => $second->id, 'employee_code' => 'EMP-HR-2', 'name' => 'Outside Employee', 'joining_date' => today()]);
        $user = User::factory()->create(['constituency_id' => $first->id, 'is_active' => true]);
        $user->assignRole('HR Manager');

        $this->actingAs($user);

        $this->assertSame(['Scoped Employee'], Employee::query()->pluck('name')->all());
        $this->assertSame(['Scoped HR AC'], Constituency::query()->pluck('name')->all());
    }
}
