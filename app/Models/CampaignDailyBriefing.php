<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDailyBriefing extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = ['constituency_id', 'briefing_date', 'status', 'priority_message', 'morning_objectives', 'target_tasks', 'target_contacts', 'target_issues', 'planned_events', 'evening_summary', 'achievements', 'blockers', 'next_day_priorities', 'created_by', 'reviewed_by', 'reviewed_at'];

    protected $casts = ['briefing_date' => 'date', 'target_tasks' => 'integer', 'target_contacts' => 'integer', 'target_issues' => 'integer', 'reviewed_at' => 'datetime'];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
