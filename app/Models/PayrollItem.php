<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use ScopedToAssemblyConstituency;

    public const PAYMENT_STATUSES = ['Pending' => 'Pending', 'On Hold' => 'On Hold', 'Paid' => 'Paid', 'Failed' => 'Failed'];

    public const PAYMENT_METHODS = ['Bank Transfer' => 'Bank Transfer', 'UPI' => 'UPI', 'Cash' => 'Cash', 'Cheque' => 'Cheque', 'Other' => 'Other'];

    protected $fillable = ['payroll_run_id', 'employee_id', 'employee_code_snapshot', 'employee_name_snapshot', 'designation_snapshot', 'salary_type_snapshot', 'salary_rate_snapshot', 'present_days', 'paid_leave_days', 'unpaid_leave_days', 'worked_hours', 'overtime_hours', 'basic_pay', 'allowances', 'incentives', 'overtime_pay', 'reimbursements', 'deductions', 'advances', 'gross_pay', 'net_pay', 'payment_status', 'payment_method', 'payment_reference', 'payment_date', 'remarks'];

    protected $casts = ['payment_date' => 'date', 'salary_rate_snapshot' => 'decimal:2', 'present_days' => 'decimal:2', 'paid_leave_days' => 'decimal:2', 'unpaid_leave_days' => 'decimal:2', 'worked_hours' => 'decimal:2', 'overtime_hours' => 'decimal:2', 'basic_pay' => 'decimal:2', 'allowances' => 'decimal:2', 'incentives' => 'decimal:2', 'overtime_pay' => 'decimal:2', 'reimbursements' => 'decimal:2', 'deductions' => 'decimal:2', 'advances' => 'decimal:2', 'gross_pay' => 'decimal:2', 'net_pay' => 'decimal:2'];

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            $item->gross_pay = round((float) $item->basic_pay + (float) $item->allowances + (float) $item->incentives + (float) $item->overtime_pay + (float) $item->reimbursements, 2);
            $item->net_pay = max(0, round((float) $item->gross_pay - (float) $item->deductions - (float) $item->advances, 2));
        });
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
