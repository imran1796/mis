<?php

namespace App\Services;

use App\Imports\MyImport;
use Illuminate\Http\UploadedFile;
use App\Interfaces\VesselInterface;
use Maatwebsite\Excel\Facades\Excel;


class VesselService
{
    protected $vesselRepository;

    public function __construct(VesselInterface $vesselRepository)
    {
        $this->vesselRepository = $vesselRepository;
    }

    public function getAllVessels()
    {
        return $this->vesselRepository->getAllVessels();
    }

    public function getVesselById($id)
    {
        return $this->vesselRepository->getVesselById($id);
    }

    // public function createVessel(UploadedFile $file): void
    // {
    //     $rows = Excel::toArray(new MyImport, $file)[0];

    //     $filteredData = [];
    //     foreach ($rows as $index => $row) {
    //         if ($index === 0 || empty($row[1]) ) {
    //             continue;
    //         }

    //         $filteredData[] = [
    //             'vessel_name'        => trim(strtoupper($row[1])),
    //             'length_overall'     => $row[2],
    //             'crane_status'      => $row[3],
    //             'nominal_capacity'   => $row[4],
    //             'imo_no'            => $row[6],
    //         ];
    //     }

    //     $this->vesselRepository->createVessel($filteredData);
    // }

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
