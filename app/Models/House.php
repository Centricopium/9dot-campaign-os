<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    protected $fillable = [
        'booth_id',
        'house_no',
        'head_of_family',
        'mobile',
        'address',
        'gps_latitude',
        'gps_longitude',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function booth(): BelongsTo
    {
        return $this->belongsTo(Booth::class);
    }

    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return trim(
            "{$this->house_no} - {$this->head_of_family}"
        );
    }

    public function getTotalVotersAttribute(): int
    {
        return $this->voters()->count();
    }

    public function getActiveVotersAttribute(): int
    {
        return $this->voters()
            ->where('is_active', true)
            ->count();
    }

    public function getVolunteerCountAttribute(): int
    {
        return $this->voters()
            ->where('is_volunteer', true)
            ->count();
    }

    public function getInfluencerCountAttribute(): int
    {
        return $this->voters()
            ->where('is_influencer', true)
            ->count();
    }

    public function getSupporterCountAttribute(): int
    {
        return $this->voters()
            ->whereIn('support_level', [
                'Strong Congress',
                'Congress Leaning',
                'Strong BJP',
                'BJP Leaning',
            ])
            ->count();
    }

    public function getOppositionCountAttribute(): int
    {
        return 0;
    }

    public function getUndecidedCountAttribute(): int
    {
        return $this->voters()
            ->where('support_level', 'Undecided')
            ->count();
    }
}