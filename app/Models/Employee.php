<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Employee extends Model
{
    use ScopedToAssemblyConstituency;

    public const EMPLOYMENT_TYPES = ['Full Time' => 'Full Time', 'Part Time' => 'Part Time', 'Contract' => 'Contract', 'Temporary' => 'Temporary', 'Field Worker' => 'Field Worker'];

    public const SALARY_TYPES = ['Monthly' => 'Monthly', 'Daily' => 'Daily Wage', 'Hourly' => 'Hourly', 'Contract' => 'Fixed Contract'];

    public const STATUSES = ['Active' => 'Active', 'On Leave' => 'On Leave', 'Inactive' => 'Inactive', 'Resigned' => 'Resigned', 'Terminated' => 'Terminated'];

    protected $fillable = ['constituency_id', 'user_id', 'employee_code', 'name', 'mobile', 'email', 'date_of_birth', 'gender', 'address', 'department', 'designation', 'employment_type', 'salary_type', 'base_salary', 'daily_rate', 'hourly_rate', 'default_allowance', 'default_deduction', 'joining_date', 'leaving_date', 'status', 'photo_path', 'identity_type', 'identity_number', 'identity_document_path', 'account_holder_name', 'bank_name', 'bank_account_number', 'ifsc_code', 'upi_id', 'notes'];

    protected $hidden = ['identity_number', 'bank_account_number', 'upi_id'];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date', 'joining_date' => 'date', 'leaving_date' => 'date', 'base_salary' => 'decimal:2', 'daily_rate' => 'decimal:2', 'hourly_rate' => 'decimal:2', 'default_allowance' => 'decimal:2', 'default_deduction' => 'decimal:2', 'identity_number' => 'encrypted', 'bank_account_number' => 'encrypted', 'upi_id' => 'encrypted'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $employee) => $employee->employee_code ??= 'EMP-'.now()->format('ym').'-'.Str::upper(Str::random(5)));
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function getMaskedBankAccountAttribute(): ?string
    {
        $value = $this->bank_account_number;

        return $value ? str_repeat('•', max(0, strlen($value) - 4)).substr($value, -4) : null;
    }
}
