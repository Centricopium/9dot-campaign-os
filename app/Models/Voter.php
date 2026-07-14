<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = [

        'house_id',

        'serial_no',
        'part_no',
        'epic_no',

        'name',
        'father_husband_name',

        'gender',
        'age',
        'dob',

        'mobile',
        'whatsapp',
        'email',

        'house_no',
        'address',

        'photo',

        'caste',
        'category',
        'religion',
        'occupation',
        'education',

        'support_level',
        'party_support',
        'party_preference',

        'is_influencer',
        'is_volunteer',

        'last_contact_date',

        'remarks',

        'is_active',

    ];

    protected $casts = [

        'dob' => 'date',
        'last_contact_date' => 'date',

        'is_influencer' => 'boolean',
        'is_volunteer' => 'boolean',
        'is_active' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function booth()
    {
        return $this->hasOneThrough(
            Booth::class,
            House::class,
            'id',
            'id',
            'house_id',
            'booth_id'
        );
    }

    public function village()
    {
        return $this->hasOneThrough(
            Village::class,
            House::class,
            'id',
            'id',
            'house_id',
            'village_id'
        );
    }

    public function constituency()
    {
        return $this->hasOneThrough(
            Constituency::class,
            House::class,
            'id',
            'id',
            'house_id',
            'constituency_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->epic_no})";
    }

    public function getSupportBadgeColorAttribute(): string
    {
        return match ($this->support_level) {

            'Strong Congress' => 'success',
            'Congress Leaning' => 'info',

            'Neutral' => 'gray',
            'Undecided' => 'warning',

            'BJP Leaning' => 'danger',
            'Strong BJP' => 'danger',

            default => 'secondary',
        };
    }
}