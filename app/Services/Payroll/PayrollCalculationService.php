<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeLeave;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculationService
{
    public function process(PayrollRun $run): PayrollRun
    {
        return DB::transaction(function () use ($run): PayrollRun {
            $run->refresh();
            $workingDays = $run->working_days ?: $this->workingDays($run->period_start, $run->period_end);
            $run->forceFill(['working_days' => $workingDays])->save();

            $employees = Employee::query()
                ->where('constituency_id', $run->constituency_id)
                ->whereIn('status', ['Active', 'On Leave'])
                ->whereDate('joining_date', '<=', $run->period_end)
                ->where(fn ($query) => $query->whereNull('leaving_date')->orWhereDate('leaving_date', '>=', $run->period_start))
                ->get();

            foreach ($employees as $employee) {
                $this->upsertItem($run, $employee, $workingDays);
            }

            $run->items()->whereNotIn('employee_id', $employees->pluck('id'))->delete();
            $run->forceFill(['status' => 'Processed', 'processed_by' => auth()->id(), 'processed_at' => now()])->save();

            return $run->fresh(['items.employee']);
        });
    }

    public function workingDays(Carbon|string $start, Carbon|string $end): int
    {
        $cursor = Carbon::parse($start)->startOfDay();
        $last = Carbon::parse($end)->startOfDay();
        $days = 0;

        while ($cursor->lte($last)) {
            if (! $cursor->isWeekend()) {
                $days++;
            }
            $cursor->addDay();
        }

        return max(1, $days);
    }

    private function upsertItem(PayrollRun $run, Employee $employee, int $workingDays): PayrollItem
    {
        $attendances = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$run->period_start, $run->period_end])
            ->get();

        $approvedLeaves = EmployeeLeave::query()
            ->where('employee_id', $employee->id)
            ->where('status', 'Approved')
            ->whereDate('starts_on', '<=', $run->period_end)
            ->whereDate('ends_on', '>=', $run->period_start)
            ->get();

        $presentDays = $attendances->sum(fn ($row): float => match ($row->status) {
            'Present', 'Holiday', 'Weekly Off' => 1,
            'Half Day' => .5,
            default => 0,
        });
        $approvedPaidLeaveDays = $approvedLeaves->where('is_paid', true)->sum(fn (EmployeeLeave $leave): int => $this->leaveWorkingDays($leave, $run));
        $approvedUnpaidLeaveDays = $approvedLeaves->where('is_paid', false)->sum(fn (EmployeeLeave $leave): int => $this->leaveWorkingDays($leave, $run));
        $paidLeaveDays = $attendances->where('status', 'Paid Leave')->count() + $approvedPaidLeaveDays;
        $unpaidLeaveDays = $attendances->whereIn('status', ['Absent', 'Unpaid Leave'])->count() + $approvedUnpaidLeaveDays;

        if ($attendances->isEmpty()) {
            $paidLeaveDays = $approvedPaidLeaveDays;
            $unpaidLeaveDays = $approvedUnpaidLeaveDays;
            $presentDays = max(0, $workingDays - $paidLeaveDays - $unpaidLeaveDays);
        }

        $payableDays = min($workingDays, max(0, $presentDays + $paidLeaveDays));
        $workedHours = $attendances->sum(function (EmployeeAttendance $row): float {
            if (! $row->check_in || ! $row->check_out) {
                return $row->status === 'Present' ? 8 : ($row->status === 'Half Day' ? 4 : 0);
            }

            $minutes = Carbon::parse($row->check_in)->diffInMinutes(Carbon::parse($row->check_out), false);

            return max(0, round($minutes / 60, 2));
        });
        $overtimeHours = (float) $attendances->sum('overtime_hours');

        $rate = match ($employee->salary_type) {
            'Daily' => (float) $employee->daily_rate,
            'Hourly' => (float) $employee->hourly_rate,
            default => (float) $employee->base_salary,
        };
        $basicPay = match ($employee->salary_type) {
            'Daily' => $rate * $payableDays,
            'Hourly' => $rate * $workedHours,
            'Contract' => $rate,
            default => $rate / max(1, $workingDays) * $payableDays,
        };
        $overtimeRate = $employee->salary_type === 'Hourly'
            ? (float) $employee->hourly_rate * 1.5
            : ((float) $employee->base_salary / max(1, $workingDays) / 8) * 1.5;

        $existing = PayrollItem::query()->firstOrNew(['payroll_run_id' => $run->id, 'employee_id' => $employee->id]);
        $variable = $existing->exists ? $existing->only(['incentives', 'reimbursements', 'advances', 'payment_status', 'payment_method', 'payment_reference', 'payment_date', 'remarks']) : [];

        $existing->fill(array_merge([
            'employee_code_snapshot' => $employee->employee_code,
            'employee_name_snapshot' => $employee->name,
            'designation_snapshot' => $employee->designation,
            'salary_type_snapshot' => $employee->salary_type,
            'salary_rate_snapshot' => $rate,
            'present_days' => round($presentDays, 2),
            'paid_leave_days' => round($paidLeaveDays, 2),
            'unpaid_leave_days' => round($unpaidLeaveDays, 2),
            'worked_hours' => round($workedHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'basic_pay' => round($basicPay, 2),
            'allowances' => (float) $employee->default_allowance,
            'overtime_pay' => round($overtimeHours * $overtimeRate, 2),
            'deductions' => (float) $employee->default_deduction,
            'payment_status' => 'Pending',
        ], $variable))->save();

        return $existing;
    }

    private function leaveWorkingDays(EmployeeLeave $leave, PayrollRun $run): int
    {
        $start = Carbon::parse($leave->starts_on)->max(Carbon::parse($run->period_start));
        $end = Carbon::parse($leave->ends_on)->min(Carbon::parse($run->period_end));

        if ($start->gt($end)) {
            return 0;
        }

        $days = 0;
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($end)) {
            if (! $cursor->isWeekend()) {
                $days++;
            }
            $cursor->addDay();
        }

        return $days;
    }
}
