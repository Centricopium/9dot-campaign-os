<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Booth extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = [
        'village_id',
        'booth_no',
        'part_no',
        'booth_name',
        'total_voters',
        'male_voters',
        'female_voters',
        'other_voters',
        'category',
        'gps_latitude',
        'gps_longitude',
        'google_map',
        'president',
        'worker',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function houses(): HasMany
    {
        return $this->hasMany(House::class);
    }

    public function voters(): HasManyThrough
    {
        return $this->hasManyThrough(
            Voter::class,
            House::class,
            'booth_id',
            'house_id',
            'id',
            'id'
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function campaignTasks(): HasMany
    {
        return $this->hasMany(CampaignTask::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Voter Counts
    |--------------------------------------------------------------------------
    */

    public function getTotalVotersAttribute(): int
    {
        return $this->voters()->count();
    }

    public function getMaleVotersAttribute(): int
    {
        return $this->voters()
            ->where('gender', 'Male')
            ->count();
    }

    public function getFemaleVotersAttribute(): int
    {
        return $this->voters()
            ->where('gender', 'Female')
            ->count();
    }

    public function getOtherVotersAttribute(): int
    {
        return $this->voters()
            ->where(function ($query) {
                $query
                    ->where('gender', 'Other')
                    ->orWhereNull('gender');
            })
            ->count();
    }
}
