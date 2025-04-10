<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class MyImport implements ToCollection, ToArray
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //
    }
    public function array(array $array)
    {
        return $array;
    }
}
