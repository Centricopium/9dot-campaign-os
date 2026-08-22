<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAuditLog extends Model
{
    use ScopedToAssemblyConstituency;

    public const UPDATED_AT = null;

    protected $table = 'security_audit_logs';

    protected $fillable = ['user_id', 'constituency_id', 'event', 'auditable_type', 'auditable_id', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent', 'request_method', 'request_path'];

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }
}
