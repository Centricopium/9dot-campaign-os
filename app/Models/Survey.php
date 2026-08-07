<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'status',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)
            ->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getTotalResponsesAttribute(): int
    {
        return $this->responses()->count();
    }
}