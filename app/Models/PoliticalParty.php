<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliticalParty extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'symbol',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function voters()
    {
        return $this->hasMany(
            Voter::class,
            'political_party_id'
        );
    }
}