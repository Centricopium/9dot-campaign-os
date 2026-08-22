<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\InternalConversation;
use App\Models\InternalMessage;
use App\Models\Role;
use App\Services\InternalMessagingService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use UnitEnum;

class InternalMessageCentre extends Page
{
    use AuthorizesPagePermission;
    use WithFileUploads;

    protected static string $requiredPermission = 'internal_message.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Collaboration';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Internal Messages';

    protected static ?string $title = 'Internal Messaging Centre';

    protected string $view = 'filament.pages.internal-message-centre';

    #[Url]
    public ?int $selectedConversationId = null;

    public bool $showCompose = false;

    public string $recipientMode = 'users';

    public array $recipientIds = [];

    public string $recipientRole = '';

    public string $subject = '';

    public string $composeBody = '';

    public string $composePriority = 'Normal';

    public $composeAttachment;

    public string $replyBody = '';

    public string $replyPriority = 'Normal';

    public $replyAttachment;

    public function mount(): void
    {
        $this->selectedConversationId ??= auth()->user()?->internalConversations()
            ->wherePivotNull('archived_at')
            ->orderByDesc('last_message_at')
            ->value('internal_conversations.id');

        if ($this->selectedConversationId) {
            app(InternalMessagingService::class)->markRead(auth()->user(), $this->selectedConversation);
        }
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $count = $user ? app(InternalMessagingService::class)->unreadCount($user) : 0;

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public function getConversationsProperty(): Collection
    {
        return auth()->user()->internalConversations()
            ->wherePivotNull('archived_at')
            ->with(['lastMessage.sender:id,name', 'participants:id,name'])
            ->orderByDesc('last_message_at')
            ->limit(50)
            ->get();
    }

    public function getSelectedConversationProperty(): ?InternalConversation
    {
        if (! $this->selectedConversationId) {
            return null;
        }

        return InternalConversation::query()
            ->forUser(auth()->user())
            ->with(['participants:id,name', 'constituency:id,name'])
            ->find($this->selectedConversationId);
    }

    public function getMessagesProperty(): Collection
    {
        return $this->selectedConversation
            ? $this->selectedConversation->messages()->with('sender:id,name')->oldest()->limit(300)->get()
            : collect();
    }

    public function getRecipientsProperty(): Collection
    {
        return app(InternalMessagingService::class)->availableRecipients(auth()->user())
            ->with('roles:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'employee_code', 'constituency_id']);
    }

    public function getRolesProperty(): Collection
    {
        return Role::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getUnreadTotalProperty(): int
    {
        return app(InternalMessagingService::class)->unreadCount(auth()->user());
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = InternalConversation::query()->forUser(auth()->user())->findOrFail($conversationId);
        $this->selectedConversationId = $conversation->id;
        app(InternalMessagingService::class)->markRead(auth()->user(), $conversation);
    }

    public function sendMessage(): void
    {
        abort_unless(auth()->user()?->can('internal_message.send'), 403);

        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'composeBody' => ['required', 'string', 'max:20000'],
            'composePriority' => ['required', 'in:Normal,Important,Urgent'],
            'composeAttachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $service = app(InternalMessagingService::class);
        $type = 'Direct';

        if ($this->recipientMode === 'role') {
            abort_unless(auth()->user()?->can('internal_message.broadcast'), 403);
            $this->validate(['recipientRole' => ['required', 'string']]);
            $recipientIds = $service->availableRecipients(auth()->user())->role($this->recipientRole)->pluck('id')->all();
            $type = 'Role Broadcast';
        } elseif ($this->recipientMode === 'all') {
            abort_unless(auth()->user()?->can('internal_message.broadcast'), 403);
            $recipientIds = $service->availableRecipients(auth()->user())->pluck('id')->all();
            $type = 'Constituency Broadcast';
        } else {
            $this->validate(['recipientIds' => ['required', 'array', 'min:1']]);
            $recipientIds = $this->recipientIds;
        }

        $attachmentPath = $this->composeAttachment?->store('private/internal-messages', 'local');
        $conversation = $service->startConversation(auth()->user(), $recipientIds, $this->subject, $this->composeBody, $this->composePriority, $type, $attachmentPath);

        $this->reset(['showCompose', 'recipientIds', 'recipientRole', 'subject', 'composeBody', 'composeAttachment']);
        $this->recipientMode = 'users';
        $this->composePriority = 'Normal';
        $this->selectedConversationId = $conversation->id;
        Notification::make()->title('Internal message sent')->success()->send();
    }

    public function sendReply(): void
    {
        abort_unless(auth()->user()?->can('internal_message.send'), 403);
        $this->validate([
            'replyBody' => ['required', 'string', 'max:20000'],
            'replyPriority' => ['required', 'in:Normal,Important,Urgent'],
            'replyAttachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $attachmentPath = $this->replyAttachment?->store('private/internal-messages', 'local');
        app(InternalMessagingService::class)->reply(auth()->user(), $this->selectedConversation, $this->replyBody, $this->replyPriority, $attachmentPath);
        $this->reset(['replyBody', 'replyAttachment']);
        $this->replyPriority = 'Normal';
    }

    public function archiveSelected(): void
    {
        if (! $this->selectedConversation) {
            return;
        }

        app(InternalMessagingService::class)->archive(auth()->user(), $this->selectedConversation);
        $this->selectedConversationId = null;
        Notification::make()->title('Conversation archived')->success()->send();
    }

    public function attachmentUrl(InternalMessage $message): string
    {
        return route('internal-messages.attachment', ['message' => $message]);
    }
}
