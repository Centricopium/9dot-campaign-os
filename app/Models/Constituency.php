<?php

namespace App\Models;
use App\Models\Village;
use Illuminate\Database\Eloquent\Model;

class Constituency extends Model
{
    protected $fillable = [
        'name',
        'state',
        'district',
        'total_voters',
        'total_booths',
        'is_active',
    ];
    public function villages()
    {
        return $this->hasMany(Village::class);
    }
}