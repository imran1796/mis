<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VesselInfos extends Model {
    protected $guarded =[];

    public function vessel(){
        return $this->belongsTo(Vessel::class,'vessel_id','id');
    }

    public function importExportCounts()
    {
        return $this->hasMany(ImportExportCount::class,'vessel_info_id','id');
    }

    public function importCount()
    {
        return $this->hasOne(ImportExportCount::class, 'vessel_info_id')
                    ->where('type', 'import');
    }

    public function exportCount()
    {
        return $this->hasOne(ImportExportCount::class, 'vessel_info_id')
                    ->where('type', 'export');
    }

    public function route(){
        return $this->belongsTo(Route::class,'route_id','id');
    }
}


