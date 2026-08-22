<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = [
        'constituency_id',
        'name',
        'village_code',
        'taluka',
        'district',
        'population',
        'total_voters',
        'total_booths',
        'category',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function booths(): HasMany
    {
        return $this->hasMany(Booth::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(VillageAlias::class);
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
    | Dynamic Voter Count
    |--------------------------------------------------------------------------
    */

    public function getTotalVotersAttribute(): int
    {
        return Voter::query()
            ->whereHas('house.booth', function ($query) {
                $query->where('village_id', $this->id);
            })
            ->count();
    }

    public function getTotalBoothsAttribute(): int
    {
        return $this->booths()->count();
    }
}
