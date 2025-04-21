<?php

namespace App\Repositories;

use App\Interfaces\RouteInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Route;

class RouteRepository implements RouteInterface
{
    public function getAllRoutes()
    {
        return Route::all();
    } 
}
