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
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}