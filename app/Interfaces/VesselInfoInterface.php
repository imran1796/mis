<?php

namespace App\Interfaces;

interface VesselInfoInterface
{
    public function getAllVesselInfos($filters = []);
    public function createVesselInfos(array $data);
    public function createImportExportCount(array $data);
    public function updateVesselInfoTime(array $data);
    public function getAllUniqueOperators();
}
