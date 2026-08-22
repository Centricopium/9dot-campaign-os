<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignIssueUpdate extends Model
{
    protected $fillable = ['campaign_issue_id', 'user_id', 'old_status', 'new_status', 'notes', 'attachments'];

    protected $casts = ['attachments' => 'array'];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(CampaignIssue::class, 'campaign_issue_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
