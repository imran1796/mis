<?php

namespace App\Repositories;

use App\Interfaces\PermissionInterface;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

// use App\Models\Permission;

class PermissionRepository implements PermissionInterface
{
    public function getAllPermissions()
    {
        try {
            return Permission::all();
        } catch (\Exception $e) {
            Log::error('Error Fetching Permissions: ' . $e->getMessage());
            return collect();
        }
    }

    public function getPermissionById($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json(['permission' => $permission], 200);
        } catch (\Exception $e) {
            Log::error('Error Fetching Permission by ID: ' . $e->getMessage());
            return response()->json(['error' => "Permission Not Found: " . $e->getMessage()], 404);
        }
    }

    public function createPermission(array $data)
    {
        \DB::beginTransaction();
        try {
            Permission::create($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created Permission'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Permission: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Permission: " . $e->getMessage()], 500);
        }
    }

    public function updatePermission($id, array $data)
    {
        \DB::beginTransaction();
        try {
            $permission = Permission::findOrFail($id);
            $permission->update($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Updated Permission'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating Permission: ' . $e->getMessage());
            return response()->json(['error' => 'Error Updating Permission: ' . $e->getMessage()], 500);
        }
    }

    public function deletePermission($permission)
    {
        \DB::beginTransaction();
        try {
            if ($permission->roles()->count() > 0) {
                Log::error('Permission: '.$permission->name.' is assigned to one or more roles and cannot be deleted!');
                return response()->json([
                    'error' => 'Permission is assigned to one or more roles and cannot be deleted!'
                ], 400);
            }

            $permission->delete();
            \DB::commit();
            return response()->json(['success' => 'Successfully Deleted Permission'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting Permission: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong! ' . $e->getMessage()
            ], 500);
        }
    }
}
