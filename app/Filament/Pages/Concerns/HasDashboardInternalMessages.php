<?php

namespace App\Filament\Pages\Concerns;

use App\Filament\Pages\InternalMessageCentre;
use App\Models\InternalMessage;
use App\Services\InternalMessagingService;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

trait HasDashboardInternalMessages
{
    public function mount(): void
    {
        $user = auth()->user();

        if (! $user?->can('internal_message.view')) {
            return;
        }

        $urgent = app(InternalMessagingService::class)
            ->unreadMessages($user)
            ->with(['sender:id,name', 'conversation:id,subject'])
            ->whereIn('priority', ['Important', 'Urgent'])
            ->latest('internal_messages.id')
            ->first();

        if (! $urgent || session()->has('internal_message_alert_'.$user->id.'_'.$urgent->id)) {
            return;
        }

        Notification::make()
            ->title($urgent->priority.' internal message')
            ->body(($urgent->sender?->name ?? 'Campaign team').' sent: '.$urgent->conversation?->subject)
            ->warning()
            ->persistent()
            ->send();

        session()->put('internal_message_alert_'.$user->id.'_'.$urgent->id, true);
    }

    public function getUnreadMessagesProperty(): Collection
    {
        $user = auth()->user();

        if (! $user?->can('internal_message.view')) {
            return collect();
        }

        return app(InternalMessagingService::class)
            ->unreadMessages($user)
            ->with(['sender:id,name', 'conversation:id,subject,type'])
            ->latest('internal_messages.id')
            ->limit(5)
            ->get();
    }

    public function getUnreadMessageCountProperty(): int
    {
        $user = auth()->user();

        return $user?->can('internal_message.view')
            ? app(InternalMessagingService::class)->unreadCount($user)
            : 0;
    }

    public function getUrgentUnreadCountProperty(): int
    {
        $user = auth()->user();

        return $user?->can('internal_message.view')
            ? app(InternalMessagingService::class)->unreadMessages($user)->whereIn('priority', ['Important', 'Urgent'])->count()
            : 0;
    }

    public function messageCentreUrl(): string
    {
        return InternalMessageCentre::getUrl();
    }

    public function messageConversationUrl(InternalMessage $message): string
    {
        return InternalMessageCentre::getUrl(['selectedConversationId' => $message->internal_conversation_id]);
    }
}
