<?php

namespace App\Interfaces;

interface ExportDataInterface
{
    public function getAllExportData($filters = []);
    public function createExportData(array $exportData);
    public function getUniqueExportData($column);
    // public function getAllExportDatas();
    // public function getExportDataById($id);
    // public function createExportData(array $data);
    // public function updateExportData($id, array $data);
    // public function deleteExportData($id);
}
