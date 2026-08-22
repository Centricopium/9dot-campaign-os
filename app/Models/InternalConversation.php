<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InternalConversation extends Model
{
    public const TYPES = ['Direct' => 'Direct', 'Group' => 'Group', 'Role Broadcast' => 'Role Broadcast', 'Constituency Broadcast' => 'Constituency Broadcast'];

    protected $fillable = ['constituency_id', 'created_by', 'subject', 'type', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'internal_conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'last_read_message_id', 'archived_at', 'muted_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(InternalMessage::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(InternalMessage::class)->latestOfMany();
    }

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('participants', fn ($participants) => $participants->whereKey($user->id));
    }

    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants->firstWhere('id', $user->id);
        $lastReadMessageId = $participant?->pivot?->last_read_message_id;

        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->when($lastReadMessageId, fn ($query, $messageId) => $query->where('id', '>', $messageId))
            ->count();
    }
}
