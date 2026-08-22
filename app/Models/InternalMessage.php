<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalMessage extends Model
{
    use SoftDeletes;

    public const PRIORITIES = ['Normal' => 'Normal', 'Important' => 'Important', 'Urgent' => 'Urgent'];

    protected $fillable = ['internal_conversation_id', 'sender_id', 'reply_to_id', 'body', 'priority', 'attachment_path'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(InternalConversation::class, 'internal_conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }
}
