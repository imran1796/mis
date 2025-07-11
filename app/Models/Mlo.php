<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mlo extends Model
{
    protected $guarded = [];
    public function mloWiseCounts()
    {
        return $this->hasMany(MloWiseCount::class, 'mlo_code', 'mlo_code');
    }
}
