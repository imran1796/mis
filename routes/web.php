<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return redirect()->route('login');
});



// Auth::routes();

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('dashboard');

    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
    Route::patch('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
    Route::patch('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);

    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('vessels', App\Http\Controllers\VesselController::class);
    Route::resource('mlos', App\Http\Controllers\MloController::class);
    
    // Route::post('users/verify/{id}', [App\Http\Controllers\UserController::class, 'verifyUser'])->name('users.verify');

    Route::group([], function () {
        Route::resource('export-data', \App\Http\Controllers\ExportDataController::class);
        Route::get('export-data/report', [\App\Http\Controllers\ExportDataController::class, 'exportDataReport'])->name('export-data.report');
        Route::post('export-data/report2', [\App\Http\Controllers\ExportDataController::class, 'exportVolByPort'])->name('export-data.report2');
        Route::post('export-data/report3', [\App\Http\Controllers\ExportDataController::class, 'exportVolByRegion'])->name('export-data.report3');
        
    });

    Route::group(['prefix' => 'uoload'], function () {
        Route::get('/mloWise', [\App\Http\Controllers\MloController::class, 'mloWiseIndex'])->name('mloWise.index');
        Route::get('/vesselWise', [\App\Http\Controllers\VesselController::class, 'vesselWiseIndex'])->name('vesselWise.index');
        Route::post('/mloWise', [\App\Http\Controllers\MloController::class, 'mloWiseStore'])->name('mloWise.store');
        Route::post('/vesselWise', [\App\Http\Controllers\VesselController::class, 'vesselWiseStore'])->name('vesselWise.store');
        
    });
    
});
