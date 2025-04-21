<?php

namespace App\Interfaces;

interface MloInterface
{
    public function getAllMlos();
    public function getMloById($id);
    public function getMloByCode(string $code);
    public function createMlo(array $data);
    public function updateMlo($id, array $data);
    public function deleteMlo($id);
    public function getAllMloWiseCount($filters = []);
    public function createMloWiseCount(array $data);
}
