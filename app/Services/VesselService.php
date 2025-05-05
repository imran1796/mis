<?php

namespace App\Services;

use App\Interfaces\VesselInterface;
use App\Repositories\DatabaseRepository;


class VesselService
{
    protected $vesselRepository, $routeService, $databaseRepository;

    public function __construct(VesselInterface $vesselRepository, RouteService $routeService, DatabaseRepository $databaseRepository)
    {
        $this->vesselRepository = $vesselRepository;
        $this->routeService = $routeService;
        $this->databaseRepository = $databaseRepository;
    }

    public function getData($model, $where = null)
    {
        if ($where) {
            return $this->databaseRepository->getDataWhere($model, [], $where);
        } else {
            return $this->databaseRepository->getAllRecords($model, []);
        }
    }

    public function getAllVessels()
    {
        return $this->vesselRepository->getAllVessels();
    }

    public function getVesselById($id)
    {
        return $this->vesselRepository->getVesselById($id);
    }

    public function getVesselByName($vesselName){
        return $this->vesselRepository->getVesselByName($vesselName);
    }

    public function createVessel(array $data)
    {
        $vessel = $this->vesselRepository->getVesselByName($data['vessel_name']);
        return $this->vesselRepository->createVessel($data);
    }

    public function updateVessel($id, array $data)
    {
        return $this->vesselRepository->updateVessel($id, $data);
    }

    public function deleteVessel($id)
    {
        return $this->vesselRepository->deleteVessel($id);
    }
    
}
