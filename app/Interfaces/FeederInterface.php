<?php

namespace App\Interfaces;

interface FeederInterface
{
    public function getAllFeeders();
    public function getFeederById($id);
    public function createFeeder(array $data);
    public function updateFeeder($id, array $data);
    public function deleteFeeder($id);
}
