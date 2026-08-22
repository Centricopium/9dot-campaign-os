<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoothOrganisation extends Model
{
    protected $table = 'booth_organisations';

    protected $fillable = [
        'booth_id',
        'voter_id',
        'role',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Booth
    |--------------------------------------------------------------------------
    */

    public function booth(): BelongsTo
    {
        return $this->belongsTo(
            Booth::class,
            'booth_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Voter
    |--------------------------------------------------------------------------
    */

    public function voter(): BelongsTo
    {
        return $this->belongsTo(
            Voter::class,
            'voter_id'
        );
    }
}