<?php

namespace App\Models;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

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

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isAssemblyAdmin(): bool
    {
        return $this->hasRole('Assembly Admin');
    }

    public function isBoothUser(): bool
    {
        return ! is_null($this->booth_id);
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

        return asset('storage/' . $this->profile_photo);
    }
}