<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\ContainerCountHelper as CCH;

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

        public function mloAllVersions()
    {
        return $this->hasMany(Mlo::class, 'mlo_code', 'mlo_code');
    }

    public function effectiveMlo()
    {
        return $this->hasOne(Mlo::class, 'mlo_code', 'mlo_code')
            ->whereColumn('effective_from', '<=', 'mlo_wise_counts.date')
            ->where(function ($q) {
                $q->whereNull('effective_to')
                ->orWhereColumn('effective_to', '>=', 'mlo_wise_counts.date');
            });
    }
    

    public function route(){
        return $this->belongsTo(Route::class,'route_id','id');
    }

    protected $appends = [
        'import_teu', 'import_box', 'export_teu', 'export_box'
    ];
    
    public function getImportTeuAttribute()
    {
        if ($this->type !== 'import') {
            return 0;
        }
        return CCH::calculateTeu($this ?? (object) []);
    }
    
    public function getImportBoxAttribute()
    {
        if ($this->type !== 'import') {
            return 0;
        }
        return CCH::calculateBox($this ?? (object) []);
    }
    
    public function getExportTeuAttribute()
    {
        if ($this->type !== 'export') {
            return 0;
        }
        return CCH::calculateTeu($this ?? (object) []);
    }
    
    public function getExportBoxAttribute()
    {
        if ($this->type !== 'export') {
            return 0;
        }
        return CCH::calculateBox($this ?? (object) []);
    }
}
