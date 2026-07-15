<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = [
        'booth_id',
        'house_no',
        'head_of_family',
        'mobile',
        'address',
        'gps_latitude',
        'gps_longitude',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getTotalVotersAttribute()
    {
        return $this->voters()->count();
    }

    public function getActiveVotersAttribute()
    {
        return $this->voters()->where('is_active', true)->count();
    }
    public function getDisplayNameAttribute(): string
    {
        return "{$this->house_no} - {$this->head_of_family}";
    }
}