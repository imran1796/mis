<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use Illuminate\Support\Facades\Log;
// use App\Models\Role;
use Spatie\Permission\Models\Role as Role;

class RoleRepository implements RoleInterface
{
    public function getAllRoles()
    {
        return Role::all();
    }

    public function getRoleById($id)
    {
        return Role::findOrFail($id);
    }

    public function createRole(array $data)
    {
    \DB::beginTransaction();
        try {
            $RoleStore = Role::create($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created Role', 'Role' => RoleStore], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Role: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Role: " . $e->getMessage()], 500);
        }
    }

    public function updateRole($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $RoleUpdate = Role::findOrFail($id);
            $RoleUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated Role',
                'Role' => $RoleUpdate
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating Role: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating Role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteRole($id)
    {
        \DB::beginTransaction();
        try {
            $Role = Role::findOrFail($id);
            $Role->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted Role'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting Role: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting Role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRolePermissions($id)
    {
        return \DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
    }
}
