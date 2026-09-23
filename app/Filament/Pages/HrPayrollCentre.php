<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\EmployeeAttendances\EmployeeAttendanceResource;
use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use App\Models\Constituency;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeLeave;
use App\Models\PayrollRun;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class HrPayrollCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'hr_dashboard.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'HR Dashboard';

    protected static ?string $title = 'HR & Payroll Centre';

    protected string $view = 'filament.pages.hr-payroll-centre';

    public string $constituencyId = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isConstituencyScoped() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getStatsProperty(): array
    {
        $employees = $this->employeeQuery();
        $todayAttendance = EmployeeAttendance::query()
            ->whereDate('attendance_date', today())
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->whereHas('employee', fn (Builder $employee): Builder => $employee->where('constituency_id', $id)));
        $currentPayroll = $this->payrollQuery()
            ->where('period_year', now()->year)
            ->where('period_month', now()->month);

        return [
            'active_employees' => (clone $employees)->where('status', 'Active')->count(),
            'present_today' => (clone $todayAttendance)->whereIn('status', ['Present', 'Half Day'])->count(),
            'pending_leaves' => EmployeeLeave::query()
                ->where('status', 'Pending')
                ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->whereHas('employee', fn (Builder $employee): Builder => $employee->where('constituency_id', $id)))
                ->count(),
            'month_payout' => (float) (clone $currentPayroll)->withSum('items', 'net_pay')->get()->sum('items_sum_net_pay'),
            'unpaid_employees' => (int) (clone $currentPayroll)->withCount(['items as unpaid_items_count' => fn (Builder $query): Builder => $query->where('payment_status', '!=', 'Paid')])->get()->sum('unpaid_items_count'),
        ];
    }

    public function getDepartmentSummaryProperty(): Collection
    {
        return $this->employeeQuery()
            ->where('status', 'Active')
            ->selectRaw("COALESCE(NULLIF(department, ''), 'Unassigned') as department_name, COUNT(*) as employee_count")
            ->groupBy('department_name')
            ->orderByDesc('employee_count')
            ->limit(8)
            ->get();
    }

    public function getRecentLeavesProperty(): Collection
    {
        return EmployeeLeave::query()
            ->with('employee:id,employee_code,name,constituency_id')
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->whereHas('employee', fn (Builder $employee): Builder => $employee->where('constituency_id', $id)))
            ->latest('id')
            ->limit(7)
            ->get();
    }

    public function getRecentPayrollsProperty(): Collection
    {
        return $this->payrollQuery()
            ->with('constituency:id,name')
            ->withCount('items')
            ->withSum('items', 'net_pay')
            ->orderByDesc('period_start')
            ->limit(6)
            ->get();
    }

    public function employeesUrl(): string
    {
        return EmployeeResource::getUrl('index');
    }

    public function createEmployeeUrl(): string
    {
        return EmployeeResource::getUrl('create');
    }

    public function attendanceUrl(): string
    {
        return EmployeeAttendanceResource::getUrl('index');
    }

    public function leavesUrl(): string
    {
        return EmployeeLeaveResource::getUrl('index');
    }

    public function payrollUrl(): string
    {
        return PayrollRunResource::getUrl('index');
    }

    public function createPayrollUrl(): string
    {
        return PayrollRunResource::getUrl('create');
    }

    private function employeeQuery(): Builder
    {
        return Employee::query()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id));
    }

    private function payrollQuery(): Builder
    {
        return PayrollRun::query()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id));
    }
}
