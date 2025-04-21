<?php

namespace App\Interfaces;

interface VesselInterface
{
    public function getAllVessels();
    public function getVesselById($id);
    public function getVesselByName(string $name);
    public function createVessel(array $data);
    public function updateVessel($id, array $data);
    public function deleteVessel($id);
    // public function getAllImportExportCounts($filters);
    public function createVesselInfos(array $data);
    public function createImportExportCount(array $data);
    public function getAllVesselInfos($filters = []);
    // public function createImportExportCount(array $data);
}
