<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignExpense extends Model
{
    use ScopedToAssemblyConstituency;

    public const PAYMENT_METHODS = ['Cash' => 'Cash', 'UPI' => 'UPI', 'Bank Transfer' => 'Bank Transfer', 'Cheque' => 'Cheque', 'Card' => 'Card', 'Other' => 'Other'];

    public const PAYMENT_STATUSES = ['Pending' => 'Pending', 'Partially Paid' => 'Partially Paid', 'Paid' => 'Paid', 'Cancelled' => 'Cancelled'];

    public const APPROVAL_STATUSES = ['Draft' => 'Draft', 'Submitted' => 'Submitted', 'Approved' => 'Approved', 'Rejected' => 'Rejected'];

    protected $fillable = ['expense_code', 'constituency_id', 'campaign_budget_id', 'campaign_event_id', 'submitted_by', 'approved_by', 'expense_date', 'category', 'vendor', 'description', 'amount', 'payment_method', 'payment_status', 'approval_status', 'approved_at', 'receipt_path', 'invoice_reference', 'notes'];

    protected $casts = ['expense_date' => 'date', 'amount' => 'decimal:2', 'approved_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $expense): void {
            $expense->expense_code ??= 'EXP-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $expense->submitted_by ??= auth()->id();
            $expense->expense_date ??= today();
        });

        static::saving(function (self $expense): void {
            if ($expense->campaign_budget_id && ! CampaignBudget::query()->whereKey($expense->campaign_budget_id)->where('constituency_id', $expense->constituency_id)->exists()) {
                throw ValidationException::withMessages(['campaign_budget_id' => 'Budget does not belong to the selected Assembly.']);
            }

            if ($expense->campaign_event_id && ! CampaignEvent::query()->whereKey($expense->campaign_event_id)->where('constituency_id', $expense->constituency_id)->exists()) {
                throw ValidationException::withMessages(['campaign_event_id' => 'Event does not belong to the selected Assembly.']);
            }
        });
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(CampaignBudget::class, 'campaign_budget_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CampaignEvent::class, 'campaign_event_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $user): void
    {
        $this->forceFill(['approval_status' => 'Approved', 'approved_by' => $user->id, 'approved_at' => now()])->save();
    }

    public function reject(User $user, ?string $notes = null): void
    {
        $this->forceFill(['approval_status' => 'Rejected', 'approved_by' => $user->id, 'approved_at' => now(), 'notes' => $notes ?: $this->notes])->save();
    }
}
