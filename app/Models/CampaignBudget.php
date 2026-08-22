<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignBudget extends Model
{
    use ScopedToAssemblyConstituency;

    public const CATEGORIES = [
        'Events & Rallies' => 'Events & Rallies',
        'Media & Advertising' => 'Media & Advertising',
        'Digital Campaign' => 'Digital Campaign',
        'Campaign Material' => 'Campaign Material',
        'Volunteer Operations' => 'Volunteer Operations',
        'Travel & Transport' => 'Travel & Transport',
        'Office & Administration' => 'Office & Administration',
        'Research & Survey' => 'Research & Survey',
        'Training' => 'Training',
        'Legal & Compliance' => 'Legal & Compliance',
        'Contingency' => 'Contingency',
        'Other' => 'Other',
    ];

    public const STATUSES = ['Draft' => 'Draft', 'Active' => 'Active', 'Closed' => 'Closed'];

    protected $fillable = ['constituency_id', 'created_by', 'title', 'category', 'allocated_amount', 'starts_on', 'ends_on', 'status', 'notes'];

    protected $casts = ['allocated_amount' => 'decimal:2', 'starts_on' => 'date', 'ends_on' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (self $budget): void {
            $budget->created_by ??= auth()->id();
            $budget->status ??= 'Active';
        });
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CampaignExpense::class);
    }

    public function getApprovedSpentAttribute(): float
    {
        return (float) ($this->expenses_sum_amount ?? $this->expenses()->where('approval_status', 'Approved')->sum('amount'));
    }

    public function getAvailableAmountAttribute(): float
    {
        return max(0, (float) $this->allocated_amount - $this->approved_spent);
    }

    public function getUtilizationPercentAttribute(): int
    {
        return (float) $this->allocated_amount > 0
            ? min(100, (int) round(($this->approved_spent / (float) $this->allocated_amount) * 100))
            : 0;
    }
}
