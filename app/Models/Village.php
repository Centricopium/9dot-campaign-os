<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = [
        'constituency_id',
        'name',
        'village_code',
        'taluka',
        'district',
        'population',
        'total_voters',
        'total_booths',
        'category',
        'latitude',
        'longitude',
        'is_active',
    ];

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }

    public function booths()
    {
        return $this->hasMany(Booth::class);
    }
   
}