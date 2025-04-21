<?php

namespace App\Repositories;

use App\Interfaces\MloInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Mlo;
use App\Models\MloWiseCount;

class MloRepository implements MloInterface
{
    public function getAllMlos()
    {
        return Mlo::all();
    }

    public function getAllMloWiseCount($filters = []){
        if (empty($filters)) {
            return collect();
        }

        $query = MloWiseCount::query();
        if (!empty($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        }

        if (!empty($filters['mlo'])) {
            $query->whereIn('mlo_code', $filters['mlo']);
        }

        if (!empty($filters['pod'])) {
            $query->whereIn('route_id', $filters['pod']);
        }

        if (!empty($filters['type'])) {
            if($filters['type'] == 'all'){
                $query->whereIn('type', ['IMPORT','EXPORT']);
            }
            else{
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
}
