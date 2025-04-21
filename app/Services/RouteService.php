<?php

namespace App\Services;

use App\Interfaces\RouteInterface;

class RouteService
{
    protected $routeRepository;

    public function __construct(RouteInterface $routeRepository)
    {
        $this->routeRepository = $routeRepository;
    }

    public function getAllRoutes()
    {
        return $this->routeRepository->getAllRoutes();
    }
}
