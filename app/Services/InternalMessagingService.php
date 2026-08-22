<?php

namespace App\Services;

use App\Models\InternalConversation;
use App\Models\InternalMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InternalMessagingService
{
    public function availableRecipients(User $sender): Builder
    {
        return User::query()
            ->where('is_active', true)
            ->whereKeyNot($sender->id)
            ->when(
                ! $sender->isSuperAdmin() && $sender->constituency_id,
                fn (Builder $query): Builder => $query->where('constituency_id', $sender->constituency_id),
            )
            ->when(
                ! $sender->isSuperAdmin() && ! $sender->constituency_id,
                fn (Builder $query): Builder => $query->whereRaw('1 = 0'),
            );
    }

    public function unreadMessages(User $user): Builder
    {
        return InternalMessage::query()
            ->select('internal_messages.*')
            ->join('internal_conversation_participants as participants', 'participants.internal_conversation_id', '=', 'internal_messages.internal_conversation_id')
            ->where('participants.user_id', $user->id)
            ->whereNull('participants.archived_at')
            ->where('internal_messages.sender_id', '!=', $user->id)
            ->where(function (Builder $query): void {
                $query->whereNull('participants.last_read_message_id')->orWhereColumn('internal_messages.id', '>', 'participants.last_read_message_id');
            });
    }

    public function unreadCount(User $user): int
    {
        return $this->unreadMessages($user)->count();
    }

    public function startConversation(User $sender, array $recipientIds, string $subject, string $body, string $priority = 'Normal', string $type = 'Direct', ?string $attachmentPath = null): InternalConversation
    {
        $recipientIds = $this->availableRecipients($sender)
            ->whereKey($recipientIds)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($recipientIds === []) {
            throw ValidationException::withMessages(['recipientIds' => 'Select at least one permitted recipient.']);
        }

        return DB::transaction(function () use ($sender, $recipientIds, $subject, $body, $priority, $type, $attachmentPath): InternalConversation {
            $conversation = InternalConversation::create([
                'constituency_id' => $sender->constituency_id,
                'created_by' => $sender->id,
                'subject' => $subject,
                'type' => count($recipientIds) > 1 && $type === 'Direct' ? 'Group' : $type,
                'last_message_at' => now(),
            ]);

            $participants = collect([$sender->id, ...$recipientIds])->unique()->mapWithKeys(fn (int $id): array => [$id => [
                'joined_at' => now(),
                'last_read_at' => $id === $sender->id ? now() : null,
            ]])->all();
            $conversation->participants()->attach($participants);

            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'body' => $body,
                'priority' => $priority,
                'attachment_path' => $attachmentPath,
            ]);
            $conversation->participants()->updateExistingPivot($sender->id, ['last_read_message_id' => $message->id]);

            return $conversation;
        });
    }

    public function reply(User $sender, InternalConversation $conversation, string $body, string $priority = 'Normal', ?string $attachmentPath = null): InternalMessage
    {
        $this->ensureParticipant($sender, $conversation);

        return DB::transaction(function () use ($sender, $conversation, $body, $priority, $attachmentPath): InternalMessage {
            $message = $conversation->messages()->create(['sender_id' => $sender->id, 'body' => $body, 'priority' => $priority, 'attachment_path' => $attachmentPath]);
            $conversation->forceFill(['last_message_at' => now()])->save();
            $this->markRead($sender, $conversation);

            return $message;
        });
    }

    public function markRead(User $user, InternalConversation $conversation): void
    {
        $this->ensureParticipant($user, $conversation);
        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
            'last_read_message_id' => $conversation->messages()->max('id'),
            'archived_at' => null,
        ]);
    }

    public function archive(User $user, InternalConversation $conversation): void
    {
        $this->ensureParticipant($user, $conversation);
        $conversation->participants()->updateExistingPivot($user->id, ['archived_at' => now()]);
    }

    private function ensureParticipant(User $user, InternalConversation $conversation): void
    {
        if (! $conversation->participants()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages(['conversation' => 'You do not have access to this conversation.']);
        }
    }
}
