<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportData extends Model
{
    protected $table = "export_datas";
    protected $fillable = [
            'mlo',
            '20ft',
            '40ft',
            '45ft',
            '20R',
            '40R',
            'commodity',
            'pod',
            'trade',
            'port_code',
            'date',
    ];
}
