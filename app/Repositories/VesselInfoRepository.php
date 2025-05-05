<?php

namespace App\Repositories;

use App\Interfaces\VesselInfoInterface;
use Illuminate\Support\Facades\Log;
use App\Models\ImportExportCount;
use App\Models\VesselInfos;
use Carbon\Carbon;

class VesselInfoRepository implements VesselInfoInterface
{
    public function getAllVesselInfos($filters = [])
    {
        $query = VesselInfos::with([
            'vessel',
            'route',
            'importExportCounts' 
            // => function ($q) use ($filters) {
            //     $baseColumns = ['id', 'vessel_info_id', 'type'];

            //     if (!empty($filters['ctn_size']) && is_array($filters['ctn_size'])) {
            //         $columns = array_merge($baseColumns, $filters['ctn_size']);
            //         $q->select($columns);
            //     } else {
            //         $q->select($baseColumns);
            //     }

            //     if (!empty($filters['shipment_type']) && is_array($filters['shipment_type'])) {
            //         $q->whereIn('type', $filters['shipment_type']);
            //     }
            // }
        ]);

        if (!empty($filters['route_id'])) {
            $query->whereIn('route_id', $filters['route_id']);
        }

        if (!empty($filters['from_date'])) {
            $fromDate = Carbon::parse($filters['from_date'])->startOfMonth();
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($filters['to_date'])) {
            $toDate = Carbon::parse($filters['to_date'])->endOfMonth();
            $query->whereDate('date', '<=', $toDate);
        }

        return $query->get();
    }

    public function getAllImportExportCount($filters = [])
    {
        if (empty($filters)) {
            return collect();
        }

        $query = ImportExportCount::query();
        if (!empty($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        }

        if (!empty($filters['vessel_name'])) {
            $query->whereIn('mlo_code', $filters['mlo']);
        }

        if (!empty($filters['pod'])) {
            $query->whereIn('route_id', $filters['pod']);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] == 'all') {
                $query->whereIn('type', ['IMPORT', 'EXPORT']);
            } else {
                $query->where('type', $filters['type']);
            }
        }

        return $query->get();
    }

    public function createVesselInfos(array $data)
    {
        return VesselInfos::create($data);
    }

    public function createImportExportCount(array $data)
    {
        return ImportExportCount::create($data);
    } 
}
