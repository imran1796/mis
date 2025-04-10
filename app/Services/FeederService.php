<?php

namespace App\Services;

use App\Interfaces\FeederInterface;

class FeederService
{
    protected $FeederRepository;

    public function __construct(FeederInterface $FeederRepository)
    {
        $this->FeederRepository = $FeederRepository;
    }

    public function getAllFeeders()
    {
        return $this->FeederRepository->getAllFeeders();
    }

    public function getFeederById($id)
    {
        return $this->FeederRepository->getFeederById($id);
    }

    public function createFeeder(array $data)
    {
        return $this->FeederRepository->createFeeder($data);
    }

    public function updateFeeder($id, array $data)
    {
        return $this->FeederRepository->updateFeeder($id, $data);
    }

    public function deleteFeeder($id)
    {
        return $this->FeederRepository->deleteFeeder($id);
    }
}
