<?php

namespace App\Repositories;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DatabaseRepository
{

    public function getAllRecords($modelName,$with =[])
    {
        $modelClass = "App\\Models\\$modelName";
        return $modelClass::with($with)->get();
    }

    public function getDataWhere($modelName,$with=[],array $where,$limit =PHP_INT_MAX){
        $modelClass = "App\\Models\\$modelName";

        $query = $modelClass::with($with)->latest()->take($limit);

        foreach ($where as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query->get();
       // return $modelClass::with($with)->get();
    }



}
