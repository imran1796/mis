<?php

namespace App\Services;

use App\Interfaces\RoleInterface;

class RoleService
{
    protected $roleRepository, $permissionService;

    public function __construct(RoleInterface $roleRepository, PermissionService $permissionService)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionService = $permissionService;
    }

    public function getAllRoles()
    {
        return $this->roleRepository->getAllRoles();
    }

    public function getRoleById($id)
    {
        return $this->roleRepository->getRoleById($id);
    }

    public function createRole(array $data)
    {
        return $this->roleRepository->createRole($data);
    }

    public function updateRole($id, array $data)
    {
        return $this->roleRepository->updateRole($id, $data);
    }

    public function deleteRole($id)
    {
        return $this->roleRepository->deleteRole($id);
    }

    public function getGroupedPermissions(){
        return $this->permissionService->getGroupedPermissions();
    }

    public function getRolePermissions($id){
        return $this->roleRepository->getRolePermissions($id);
    }
}
