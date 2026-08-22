<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Constituency extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = [
        'name',
        'state',
        'district',
        'total_voters',
        'total_booths',
        'is_active',
    ];

    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    public function campaignTasks(): HasMany
    {
        return $this->hasMany(CampaignTask::class);
    }
}
