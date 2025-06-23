<?php

namespace App\Repositories;

use App\Interfaces\MloInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Mlo;
use App\Models\MloWiseCount;
use Carbon\Carbon;

class MloRepository implements MloInterface
{
    public function getAllMlos()
    {
        return Mlo::all();
    }

    public function getAllMloWiseCount($filters = [])
    {
        if (empty($filters)) {
            return collect();
        }

        $query = MloWiseCount::with('mlo');
        if (!empty($filters['from_date'])) {
            $fromDate = Carbon::createFromFormat('d-M-Y', '01-' . $filters['from_date'])->startOfMonth();
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($filters['to_date'])) {
            $toDate = Carbon::createFromFormat('d-M-Y', '01-' . $filters['to_date'])->endOfMonth();
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($filters['mlos'])) {
            $query->whereIn('mlo_code', $filters['mlos']);
        }

        if (!empty($filters['route_id'])) {
            $query->whereIn('route_id', $filters['route_id']);
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

    public function getMloById($id)
    {
        return Mlo::findOrFail($id);
    }

    public function getMloByCode(string $code)
    {
        return Mlo::where('mlo_code', $code)->latest()->first();
    }

    public function createMlo(array $data)
    {
        \DB::beginTransaction();
        try {
            // foreach ($data as $i) {
            Mlo::updateOrCreate(
                [
                    'line_belongs_to' => $data['line_belongs_to'],
                    'mlo_code'     => $data['mlo_code'],
                ],
                [
                    'mlo_details'     => $data['mlo_details'],
                    'effective_from' => now(),
                ]
            );
            // }
            \DB::commit();
            return response()->json(['success' => 'Successfully Created Mlo'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Mlo: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Mlo: " . $e->getMessage()], 500);
        }
    }

    public function updateMlo($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $mloUpdate = Mlo::findOrFail($id);
            $mloUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated Mlo',
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating Mlo: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating Mlo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteMlo($id)
    {
        \DB::beginTransaction();
        try {
            $Mlo = Mlo::findOrFail($id);
            $Mlo->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted Mlo'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting Mlo: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting Mlo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createMloWiseCount(array $data)
    {
        \DB::beginTransaction();
        try {
            MloWiseCount::insert($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Uploaded'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Uploading: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Uploading: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDistinctMloWiseDates($filters=[]){
        return MloWiseCount::select('date', 'route_id')
        ->with('route')
        ->distinct('date')
        ->orderByDesc('date')
        ->when(!empty($filters['route_id']), function ($q) use ($filters) {
            $q->whereIn('route_id',$filters['route_id']);
        })
        ->when(!empty($filters['from_date']), function ($q) use ($filters) {
            $q->whereDate('date', '>=',Carbon::parse($filters['from_date'])->startOfMonth());
        })
        ->when(!empty($filters['to_date']), function ($q) use ($filters) {
            $q->whereDate('date', '<=', Carbon::parse($filters['to_date'])->startOfMonth());
        })
        ->get()
        ->groupBy(['date','route_id']);
    }
}
