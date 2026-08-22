<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',

    'mobile',
    'designation',
    'employee_code',
    'profile_photo',

    'constituency_id',
    'village_id',
    'booth_id',

    'is_super_admin',
    'is_active',

    'last_login_at',
    'notes',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /**
     * Attribute Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',

            'last_login_at' => 'datetime',

            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function booth(): BelongsTo
    {
        return $this->belongsTo(Booth::class);
    }

    public function assignedCampaignTasks(): HasMany
    {
        return $this->hasMany(CampaignTask::class, 'assigned_to');
    }

    public function internalConversations(): BelongsToMany
    {
        return $this->belongsToMany(InternalConversation::class, 'internal_conversation_participants')
            ->withPivot(['joined_at', 'last_read_at', 'last_read_message_id', 'archived_at', 'muted_at'])
            ->withTimestamps();
    }

    public function sentInternalMessages(): HasMany
    {
        return $this->hasMany(InternalMessage::class, 'sender_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin || $this->hasRole('Super Admin');
    }

    public function isAssemblyAdmin(): bool
    {
        return $this->hasRole('Assembly Admin');
    }

    public function isBoothUser(): bool
    {
        return ! is_null($this->booth_id);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        if ($this->employee_code) {
            return "{$this->name} ({$this->employee_code})";
        }

        return $this->name;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (blank($this->profile_photo)) {
            return null;
        }

        return asset('storage/'.$this->profile_photo);
    }
}
