<?php

namespace App\Services;

use App\Imports\MyImport;
use App\Interfaces\MloInterface;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class MloService
{
    protected $mloRepository;

    public function __construct(MloInterface $mloRepository)
    {
        $this->mloRepository = $mloRepository;
    }

    public function getAllMlos()
    {
        return $this->mloRepository->getAllMlos();
    }

    public function getMloById($id)
    {
        return $this->mloRepository->getMloById($id);
    }

    // public function createMlo(UploadedFile $file): void
    // {
    //     $rows = Excel::toArray(new MyImport, $file)[0];

    //     $filteredData = [];
    //     foreach ($rows as $index => $row) {
    //         if (empty($row[2])) {
    //             continue;
    //         }

    //         $filteredData[] = [
    //             'line_belongs_to'        => trim(strtoupper($row[1])),
    //             'mlo_code'     => trim(strtoupper($row[0])),
    //             'mlo_details'      => trim(strtoupper($row[2])),
    //         ];
    //     }

    //     $this->mloRepository->createMlo($filteredData);
    // }

    public function createMlo(array $data)
    {
        $mlo = $this->mloRepository->getMloByCode($data['mlo_code']);
        if ($mlo) {
            $mlo['effective_to'] = now()->subDay();
            $this->mloRepository->updateMlo($mlo->id,$mlo->toArray());
        }
        return $this->mloRepository->createMlo($data);
    }

    public function updateMlo($id, array $data)
    {
        return $this->mloRepository->updateMlo($id, $data);
    }

    public function deleteMlo($id)
    {
        return $this->mloRepository->deleteMlo($id);
    }
}
