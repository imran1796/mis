<?php

namespace App\Services;

use App\Interfaces\PermissionInterface;

class PermissionService
{
    protected $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function getAllPermissions()
    {
        return $this->permissionRepository->getAllPermissions();
    }

    public function getPermissionById($id)
    {
        return $this->permissionRepository->getPermissionById($id);
    }

    public function createPermission(array $data)
    {
        return $this->permissionRepository->createPermission($data);
    }

    public function updatePermission($id, array $data)
    {
        return $this->permissionRepository->updatePermission($id, $data);
    }

    public function deletePermission($permission)
    {
        return $this->permissionRepository->deletePermission($permission);
    }

    /*
        from: user-create, user-edit, role-edit
        to: user[user-create, user-edit], role[role-create, role-edit]
            assending sorted
    */
    public function getGroupedPermissions()
    {
        $permissions = $this->permissionRepository->getAllPermissions();
        $groupedPermissions = collect($permissions)
            ->groupBy(fn($permission) => explode('-', $permission->name)[0])
            ->sortKeys();
        return $groupedPermissions;
    }
}
