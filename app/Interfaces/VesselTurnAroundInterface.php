<?php

namespace App\Interfaces;

interface VesselTurnAroundInterface
{
    public function getAllVesselTurnArounds($filters = []);
    public function getVesselTurnAroundById($id);
    public function createVesselTurnAround(array $data);
    public function updateVesselTurnAround($id, array $data);
    public function deleteVesselTurnAround($id);
}
