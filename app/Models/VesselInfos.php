<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\ContainerCountHelper as CCH;

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

    protected $appends = [
        'import_teu', 'import_box', 'export_teu', 'export_box'
    ];
    
    public function getImportTeuAttribute()
    {
        $import = $this->importExportCounts->firstWhere('type', 'import');
        return CCH::calculateTeu($import ?? (object) []);
    }
    
    public function getImportBoxAttribute()
    {
        $import = $this->importExportCounts->firstWhere('type', 'import');
        return CCH::calculateBox($import ?? (object) []);
    }
    
    public function getExportTeuAttribute()
    {
        $export = $this->importExportCounts->firstWhere('type', 'export');
        return CCH::calculateTeu($export ?? (object) []);
    }
    
    public function getExportBoxAttribute()
    {
        $export = $this->importExportCounts->firstWhere('type', 'export');
        return CCH::calculateBox($export ?? (object) []);
    }
}


