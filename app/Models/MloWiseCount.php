<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MloWiseCount extends Model
{
    protected $fillable = [
        'route_id',
        'date',
        'mlo_code',
        'type',
        'dc20',
        'dc40',
        'dc45',
        'r20',
        'r40',
        'mty20',
        'mty40',
    ];

    public function mlo()
    {
        return $this->belongsTo(Mlo::class, 'mlo_code', 'mlo_code');
    }
}
