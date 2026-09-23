<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use ScopedToAssemblyConstituency;

    public const STATUSES = ['Present' => 'Present', 'Half Day' => 'Half Day', 'Absent' => 'Absent', 'Paid Leave' => 'Paid Leave', 'Unpaid Leave' => 'Unpaid Leave', 'Holiday' => 'Holiday', 'Weekly Off' => 'Weekly Off'];

    protected $fillable = ['employee_id', 'attendance_date', 'status', 'check_in', 'check_out', 'overtime_hours', 'marked_by', 'remarks'];

    protected $casts = ['attendance_date' => 'date', 'overtime_hours' => 'decimal:2'];

    protected static function booted(): void
    {
        static::creating(fn (self $attendance) => $attendance->marked_by ??= auth()->id());
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
