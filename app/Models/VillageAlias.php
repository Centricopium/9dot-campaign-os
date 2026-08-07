<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageAlias extends Model
{
    protected $fillable = [
        'village_id',
        'alias',
    ];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}