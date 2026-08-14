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
        'surname',
        'father_husband_name',
        'relation_type',
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
        'religion',
        'category',
        'occupation',
        'education',

        'support_level',
        'political_party_id',
        'party_preference',

        'is_influencer',
        'is_volunteer',

        'last_contact_date',

        'voter_type',
        'blood_group',

        'disability',

        'government_scheme',
        'government_scheme_name',

        'facebook',
        'instagram',
        'twitter',

        'family_members',

        'booth_committee_role',
        'panna_pramukh',
        'polling_agent',

        'priority',
        'next_followup',

        'latitude',
        'longitude',

        'internal_notes',

        'remarks',

        'is_active',
    ];


    protected $casts = [

        'dob' => 'date',

        'last_contact_date' => 'date',

        'next_followup' => 'date',

        'is_influencer' => 'boolean',

        'is_volunteer' => 'boolean',

        'disability' => 'boolean',

        'government_scheme' => 'boolean',

        'is_active' => 'boolean',

    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    public function house()
    {
        return $this->belongsTo(
            House::class,
            'house_id'
        );
    }


    public function politicalParty()
    {
        return $this->belongsTo(
            PoliticalParty::class,
            'political_party_id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */


    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->epic_no})";
    }



    public function getSupportBadgeColorAttribute(): string
    {
        return match ($this->support_level) {

            'Strong Support' => 'success',

            'Moderate Support' => 'success',

            'Leaning Support' => 'info',

            'Neutral' => 'gray',

            'Undecided' => 'warning',

            'Leaning Opposition' => 'warning',

            'Moderate Opposition' => 'danger',

            'Strong Opposition' => 'danger',

            default => 'gray',

        };
    }
}