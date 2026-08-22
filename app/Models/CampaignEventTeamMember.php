<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignEventTeamMember extends Model
{
    protected $fillable = ['campaign_event_id', 'user_id', 'role', 'attendance_status', 'checked_in_at', 'notes'];

    protected $casts = ['checked_in_at' => 'datetime'];

    public function campaignEvent(): BelongsTo
    {
        return $this->belongsTo(CampaignEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
