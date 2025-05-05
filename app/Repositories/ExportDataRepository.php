<?php

namespace App\Repositories;

use App\Interfaces\ExportDataInterface;
use Illuminate\Support\Facades\Log;
use App\Models\ExportData;
use Illuminate\Database\Eloquent\Collection;

class ExportDataRepository implements ExportDataInterface
{
    public function getAllExportData($filters = [])
    {
        // dd($filters);
        if (empty($filters)) {
            return collect();
        }

        $query = ExportData::query();
        if (!empty($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        }

        if (!empty($filters['commodity'])) {
            $query->whereIn('commodity', $filters['commodity']);
        }

        if (!empty($filters['pod'])) {
            $query->whereIn('pod', $filters['pod']);
        }

        if (!empty($filters['region'])) {
            $query->whereIn('trade', $filters['region']);
        }

        if (!empty($filters['mlo'])) {
            $query->whereIn('mlo', $filters['mlo']);
        }

        return $query->get();
    }

    // public function getAllExportData($filters = []): Collection
    // {
    //     return ExportData::query()
    //         ->when($filters['from_date'] ?? null, fn($q, $v) => $q->whereDate('date', '>=', $v))
    //         ->when($filters['to_date'] ?? null, fn($q, $v) => $q->whereDate('date', '<=', $v))
    //         ->when($filters['commodity'] ?? null, fn($q, $v) => $q->whereIn('commodity', $v))
    //         ->when($filters['pod'] ?? null, fn($q, $v) => $q->whereIn('pod', $v))
    //         ->when($filters['mlo'] ?? null, fn($q, $v) => $q->whereIn('mlo', $v))
    //         ->get();
    // }

    public function createExportData(array $exportData)
    {
        \DB::beginTransaction();
        try {

            ExportData::insert($exportData);

            \DB::commit();
            return response()->json(['success' => 'Successfully Created Export Data'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Export Data: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Export Data: " . $e->getMessage()], 500);
        }
    }

    public function getUniqueExportData($column_name)
    {
        return ExportData::query()
            ->select($column_name)
            ->distinct()
            ->pluck($column_name);
    }
}
