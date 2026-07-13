<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    protected $fillable = [
        'village_id',
        'booth_no',
        'booth_name',
        'total_voters',
        'male_voters',
        'female_voters',
        'other_voters',
        'category',
        'gps_latitude',
        'gps_longitude',
        'google_map',
        'president',
        'worker',
        'is_active',
    ];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
    
    public function voters()
    {
        return $this->hasMany(Voter::class);
    }
}