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
        'is_active',
    ];

    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }
}