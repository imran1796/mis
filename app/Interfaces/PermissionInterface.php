<?php

namespace App\Interfaces;

interface PermissionInterface
{
    public function getAllPermissions();
    public function getPermissionById($id);
    public function createPermission(array $data);
    public function updatePermission($id, array $data);
    public function deletePermission($permission);
}
