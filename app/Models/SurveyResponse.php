<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use ScopedToAssemblyConstituency;
    protected $fillable = [
        'survey_id',
        'user_id',
        'voter_id',
        'house_id',
        'latitude',
        'longitude',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'latitude'     => 'float',
        'longitude'    => 'float',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    public function getLocationAttribute(): ?string
    {
        if (! $this->latitude || ! $this->longitude) {
            return null;
        }

        return "{$this->latitude}, {$this->longitude}";
    }

    public function getAnswerCountAttribute(): int
    {
        return $this->answers()->count();
    }
}
