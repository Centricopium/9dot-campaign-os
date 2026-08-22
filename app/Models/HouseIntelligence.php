<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseIntelligence extends Model
{

    protected $fillable = [

        'house_id',

        'family_score',

        'family_type',

        'conversion_probability',

        'ai_priority',

        'family_influencer_id',

        'last_visit_date',

        'next_followup_date',

        'ai_recommendation',

    ];



    public function house()
    {
        return $this->belongsTo(House::class);
    }


    public function influencer()
    {
        return $this->belongsTo(
            Voter::class,
            'family_influencer_id'
        );
    }

}