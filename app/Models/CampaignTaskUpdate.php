<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTaskUpdate extends Model
{
    protected $fillable = [
        'campaign_task_id',
        'user_id',
        'old_status',
        'new_status',
        'completion_percent',
        'notes',
        'proof_files',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'completion_percent' => 'integer',
        'proof_files' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(CampaignTask::class, 'campaign_task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
