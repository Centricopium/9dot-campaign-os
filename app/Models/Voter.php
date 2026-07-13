<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = [

        'house_id',

        'serial_no',
        'epic_no',

        'name',
        'father_husband_name',

        'gender',
        'age',
        'dob',

        'mobile',
        'whatsapp',

        'house_no',
        'address',

        'caste',
        'religion',
        'occupation',
        'education',

        'support_level',
        'party_support',

        'is_influencer',
        'remarks',

        'is_active',

    ];

    protected $casts = [

        'dob' => 'date',
        'is_influencer' => 'boolean',
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
            'id',        // House.id
            'id',        // Booth.id
            'house_id',  // voters.house_id
            'booth_id'   // houses.booth_id
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
}