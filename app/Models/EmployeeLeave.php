<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use ScopedToAssemblyConstituency;

    public const TYPES = ['Casual' => 'Casual', 'Sick' => 'Sick', 'Earned' => 'Earned', 'Comp Off' => 'Comp Off', 'Unpaid' => 'Unpaid', 'Other' => 'Other'];

    public const STATUSES = ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected', 'Cancelled' => 'Cancelled'];

    protected $fillable = ['employee_id', 'leave_type', 'starts_on', 'ends_on', 'days', 'is_paid', 'status', 'reason', 'approved_by', 'approved_at', 'approval_notes'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'days' => 'decimal:2', 'is_paid' => 'boolean', 'approved_at' => 'datetime'];

    protected static function booted(): void
    {
        static::saving(function (self $leave): void {
            if ($leave->starts_on && $leave->ends_on) {
                $leave->days = Carbon::parse($leave->starts_on)->diffInDays(Carbon::parse($leave->ends_on)) + 1;
            }
            $leave->is_paid = $leave->leave_type !== 'Unpaid' && $leave->is_paid;
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
