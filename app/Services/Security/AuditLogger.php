<?php

namespace App\Services\Security;

use App\Models\Booth;
use App\Models\CampaignIssue;
use App\Models\CampaignTask;
use App\Models\Constituency;
use App\Models\House;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Models\Village;
use App\Models\Voter;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    private const REDACTED_FIELDS = ['password', 'remember_token', 'api_token', 'two_factor_secret', 'two_factor_recovery_codes', 'mobile', 'whatsapp', 'email', 'contact_mobile', 'internal_notes'];

    public function modelEvent(Model $model, string $event): void
    {
        $changes = $event === 'updated' ? $model->getChanges() : ($event === 'created' ? $model->getAttributes() : $model->getOriginal());
        unset($changes['updated_at'], $changes['created_at']);
        if ($event === 'updated' && $changes === []) {
            return;
        }

        $old = [];
        if ($event === 'updated') {
            foreach (array_keys($changes) as $key) {
                $old[$key] = $model->getOriginal($key);
            }
        }

        $this->write($event, class_basename($model).' '.$event, $model, $old, $changes);
    }

    public function authentication(string $event, ?User $user, string $description): void
    {
        $this->write($event, $description, $user, [], []);
    }

    public function write(string $event, string $description, ?Model $model = null, array $old = [], array $new = []): void
    {
        SecurityAuditLog::query()->create([
            'user_id' => auth()->id() ?: ($model instanceof User ? $model->id : null),
            'constituency_id' => $this->constituencyId($model),
            'event' => $event,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->getKey(),
            'description' => $description,
            'old_values' => $this->sanitize($old),
            'new_values' => $this->sanitize($new),
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : mb_substr((string) request()->userAgent(), 0, 1000),
            'request_method' => app()->runningInConsole() ? null : request()->method(),
            'request_path' => app()->runningInConsole() ? null : request()->path(),
        ]);
    }

    private function sanitize(array $values): array
    {
        foreach ($values as $key => $value) {
            if (in_array(strtolower((string) $key), self::REDACTED_FIELDS, true)) {
                $values[$key] = '[REDACTED]';
            } elseif (is_array($value) && str_contains(strtolower((string) $key), 'proof')) {
                $values[$key] = '[PRIVATE FILE LIST]';
            }
        }

        return $values;
    }

    private function constituencyId(?Model $model): ?int
    {
        if (! $model) {
            return auth()->user()?->constituency_id;
        }
        if ($model instanceof Constituency) {
            return (int) $model->id;
        }
        if ($model->getAttribute('constituency_id')) {
            return (int) $model->getAttribute('constituency_id');
        }
        if ($model instanceof Village) {
            return (int) $model->constituency_id;
        }
        if ($model instanceof Booth) {
            return (int) $model->village?->constituency_id;
        }
        if ($model instanceof House) {
            return (int) $model->booth?->village?->constituency_id;
        }
        if ($model instanceof Voter) {
            return (int) $model->house?->booth?->village?->constituency_id;
        }
        if ($model instanceof CampaignTask || $model instanceof CampaignIssue) {
            return (int) $model->constituency_id;
        }

        return auth()->user()?->constituency_id;
    }
}
