<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Constituency;
use App\Models\Village;
use App\Models\Booth;


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

    'is_active',
    'last_login_at',
    'notes',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
use HasFactory, Notifiable, HasRoles;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

        'last_login_at' => 'datetime',

        'is_active' => 'boolean',
    ];
}
public function constituency()
{
    return $this->belongsTo(Constituency::class);
}

public function village()
{
    return $this->belongsTo(Village::class);
}

public function booth()
{
    return $this->belongsTo(Booth::class);
}
public function getProfilePhotoUrlAttribute(): ?string
{
    if (! $this->profile_photo) {
        return null;
    }

    return asset('storage/' . $this->profile_photo);
}
}
