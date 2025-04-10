<?php

namespace App\Repositories;

use App\Interfaces\MloInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Mlo;

class MloRepository implements MloInterface
{
    public function getAllMlos()
    {
        return Mlo::all();
    }

    public function getMloById($id)
    {
        return Mlo::findOrFail($id);
    }

    public function getMloByCode(string $code)
    {
        return Mlo::where('mlo_code',$code)->latest()->first();
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
}
