<?php

namespace App\Providers;

use App\Interfaces\ExportDataInterface;
use App\Interfaces\MloInterface;
use App\Interfaces\PermissionInterface;
use App\Interfaces\RoleInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\VesselInterface;
use App\Repositories\ExportDataRepository;
use App\Repositories\MloRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\VesselRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(PermissionInterface::class, PermissionRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(ExportDataInterface::class, ExportDataRepository::class);
        $this->app->bind(VesselInterface::class, VesselRepository::class);
        $this->app->bind(MloInterface::class, MloRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
