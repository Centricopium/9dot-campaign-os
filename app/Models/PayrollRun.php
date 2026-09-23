<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PayrollRun extends Model
{
    use ScopedToAssemblyConstituency;

    public const STATUSES = ['Draft' => 'Draft', 'Processed' => 'Processed', 'Approved' => 'Approved', 'Paid' => 'Paid', 'Cancelled' => 'Cancelled'];

    protected $fillable = ['run_code', 'constituency_id', 'period_year', 'period_month', 'period_start', 'period_end', 'working_days', 'status', 'processed_by', 'approved_by', 'processed_at', 'approved_at', 'paid_at', 'notes'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'processed_at' => 'datetime', 'approved_at' => 'datetime', 'paid_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $run): void {
            $run->run_code ??= 'PAY-'.sprintf('%04d%02d', $run->period_year, $run->period_month).'-'.Str::upper(Str::random(4));
            $run->status ??= 'Draft';
        });
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start?->format('F Y') ?? sprintf('%02d/%04d', $this->period_month, $this->period_year);
    }
}
