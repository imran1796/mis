<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
    // Route::post('users/verify/{id}', [App\Http\Controllers\UserController::class, 'verifyUser'])->name('users.verify');

    Route::resource('export-data', \App\Http\Controllers\ExportDataController::class);
    Route::group(['prefix' => 'export-data/report/'], function () {
        Route::post('/port', [\App\Http\Controllers\ExportDataController::class, 'exportVolByPort'])->name('export-data.report.port');
        Route::post('/region', [\App\Http\Controllers\ExportDataController::class, 'exportVolByRegion'])->name('export-data.report.region');
    });
    
    Route::resource('mlos', App\Http\Controllers\MloController::class);
    Route::group(['prefix' => 'mlo-wise'], function () {
        Route::get('/index', [\App\Http\Controllers\MloController::class, 'indexMloWiseCount'])->name('mloWiseCount.index');
        Route::get('/create', [\App\Http\Controllers\MloController::class, 'createMloWiseCount'])->name('mloWiseCount.create');
        Route::post('/store', [\App\Http\Controllers\MloController::class, 'storeMloWiseCount'])->name('mloWiseCount.store');
        Route::get('/report/summary', [\App\Http\Controllers\MloController::class, 'reportMloWiseSummary'])->name('mloWiseCount.report.summary');

    });
    
    Route::resource('vessels', App\Http\Controllers\VesselController::class);
    Route::group(['prefix' => 'vessel-info'], function () {
        Route::get('/index', [\App\Http\Controllers\VesselController::class, 'indexVesselInfo'])->name('vesselInfo.index');
        Route::get('/create', [\App\Http\Controllers\VesselController::class, 'createVesselInfo'])->name('vesselInfo.create');
        Route::post('/store', [\App\Http\Controllers\VesselController::class, 'storeVesselInfo'])->name('vesselInfo.store');
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/index', [\App\Http\Controllers\VesselController::class, 'indexReport'])->name('reports.index');
        Route::get('/operator-wise-lifting', [\App\Http\Controllers\VesselController::class, 'operatorWiseLifting'])->name('reports.operator-wise-lifting');
        Route::get('/soc-inout-bound', [\App\Http\Controllers\VesselController::class, 'socInOutBound'])->name('reports.soc-inout-bound');
        Route::get('/vessel-turn-around-time', [\App\Http\Controllers\VesselController::class, 'vesselTurnAroundTime'])->name('reports.vessel-turn-around');
        Route::get('/market-competitors', [\App\Http\Controllers\VesselController::class, 'marketCompetitors'])->name('reports.market-competitors');
        Route::get('/soc-outbound-market-strategy', [\App\Http\Controllers\VesselController::class, 'socOutboundMarketStrategy'])->name('reports.soc-outbound-market');
        Route::get('/mlo-wise-summary', [\App\Http\Controllers\MloController::class, 'mloWiseSummary'])->name('reports.mlo-wise-summary');

    });


});
