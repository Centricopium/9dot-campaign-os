@if(auth()->user()?->can('internal_message.view'))
    @php($internalUnread = app(\App\Services\InternalMessagingService::class)->unreadCount(auth()->user()))
    <a href="{{ \App\Filament\Pages\InternalMessageCentre::getUrl() }}" wire:navigate title="Internal Messages" style="position:relative;display:inline-flex;width:38px;height:38px;align-items:center;justify-content:center;border:1px solid rgba(124,58,237,.22);border-radius:12px;color:#7c3aed;background:rgba(124,58,237,.07);font-size:17px;text-decoration:none">
        <span aria-hidden="true">✉</span>
        @if($internalUnread > 0)<span style="position:absolute;top:-5px;right:-5px;min-width:18px;height:18px;padding:2px 5px;border:2px solid var(--fi-body-bg,#fff);border-radius:999px;color:#fff;background:#dc2626;font-size:8px;font-weight:900;line-height:12px;text-align:center">{{ $internalUnread > 99 ? '99+' : $internalUnread }}</span>@endif
    </a>
@endif
