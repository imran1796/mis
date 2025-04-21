<?php

namespace App\Http\Controllers;

use App\Services\RouteService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    protected $routeService;
    public function __construct(RouteService $routeService){
        $this->routeService = $routeService;
    }

    public function getAllRoutes(){
        return $this->routeService->getAllRoutes();
    }
}
