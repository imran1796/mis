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
}
