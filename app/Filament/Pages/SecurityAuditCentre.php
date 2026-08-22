<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Constituency;
use App\Models\SecurityAuditLog;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class SecurityAuditCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'audit.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Security Audit';

    protected static ?string $title = 'Security & Activity Audit Centre';

    protected string $view = 'filament.pages.security-audit-centre';

    public string $constituencyId = '';

    public string $event = '';

    public string $userId = '';

    public string $search = '';

    public function mount(): void
    {
        $u = auth()->user();
        if ($u?->isAssemblyAdmin() && ! $u->isSuperAdmin()) {
            $this->constituencyId = (string) $u->constituency_id;
        }
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getUsersProperty(): Collection
    {
        return User::query()->when($this->constituencyId, fn (Builder $q, $id) => $q->where('constituency_id', $id))->orderBy('name')->get(['id', 'name']);
    }

    public function getLogsProperty(): Collection
    {
        return $this->query()->with(['user', 'constituency'])->latest('created_at')->limit(100)->get();
    }

    public function getStatsProperty(): array
    {
        $q = $this->query();

        return ['today' => (clone $q)->whereDate('created_at', today())->count(), 'logins' => (clone $q)->where('event', 'login')->whereDate('created_at', today())->count(), 'failed' => (clone $q)->where('event', 'login_failed')->whereDate('created_at', today())->count(), 'updates' => (clone $q)->where('event', 'updated')->whereDate('created_at', today())->count(), 'deletes' => (clone $q)->where('event', 'deleted')->whereDate('created_at', today())->count(), 'activeUsers' => User::query()->where('is_active', true)->when($this->constituencyId, fn (Builder $q, $id) => $q->where('constituency_id', $id))->count()];
    }

    private function query(): Builder
    {
        return SecurityAuditLog::query()->when($this->constituencyId, fn (Builder $q, $id) => $q->where('constituency_id', $id))->when($this->event, fn (Builder $q, $v) => $q->where('event', $v))->when($this->userId, fn (Builder $q, $id) => $q->where('user_id', $id))->when($this->search, fn (Builder $q, $s) => $q->where('description','like',"%{$s}%"));
    }
}
