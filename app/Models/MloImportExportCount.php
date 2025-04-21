<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MloImportExportCount extends Model
{
    protected $fillable = [
        'route', 'date', 'mlo_code', 'type',
        'dc20', 'dc40', 'dc45', 'r20', 'r40',
        'mty20', 'mty40',
    ];
}
