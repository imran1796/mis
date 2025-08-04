<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportExportCount extends Model
{
    protected $guarded = [];
    
    public function vesselInfos()
    {
        return $this->belongsTo(VesselInfos::class,'vessel_info_id','id');
    }
}
