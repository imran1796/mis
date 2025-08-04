<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class GetUniqueYearHelper
{
    /**
     * Get distinct years from one or more Eloquent models.
     *
     * @param array $models Array of models with optional custom date field
     *     Example: [VesselInfos::class, ExportData::class]
     *              or    [['model' => ExportData::class, 'column' => 'export_date']]
     * @param string $defaultColumn Default date field to extract years from
     * @return array Sorted unique years
     */
    public static function getUniqueYears(array $models, string $defaultColumn = 'date'): array
    {
        $years = collect();

        foreach ($models as $item) {
            if (is_array($item)) {
                $modelClass = $item['model'];
                $column = $item['column'] ?? $defaultColumn;
            } else {
                $modelClass = $item;
                $column = $defaultColumn;
            }

            $years = $years->merge(
                $modelClass::selectRaw("YEAR($column) as year")
                    ->distinct()
                    ->pluck('year')
            );
        }

        return $years->unique()->sortDesc()->values()->toArray();
    }
}
