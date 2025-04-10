<?php

namespace App\Http\Controllers;

use App\Exports\ExportMarketScenarioByPort;
use App\Exports\RegionWiseExportCounts;
use App\Models\ExportData;
use App\Services\ExportDataService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportDataController extends Controller
{
    protected $exportDataService;

    public function __construct(ExportDataService $exportDataService){
        $this->exportDataService = $exportDataService;

        // $this->middleware(
        //     'permission:export-data',
        //     ['only' => [
        //         'index',
        //         'create',
        //         'store',
        //     ]]
        // );

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'commodity', 'mlo', 'pod', 'report_type']);
        $exportDatas = $this->exportDataService->getAllExportData($filters);
        $commodities = $this->exportDataService->getUniqueExportData('commodity');
        $mlos = $this->exportDataService->getUniqueExportData('mlo');
        $pods = $this->exportDataService->getUniqueExportData('pod');
        return view('export-data.index',compact('exportDatas','commodities','mlos','pods'));
    }

    // public function exportDataReport(Request){

    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('export-data.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->exportDataService->createExportData($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExportData  $exportData
     * @return \Illuminate\Http\Response
     */
    public function show(ExportData $exportData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExportData  $exportData
     * @return \Illuminate\Http\Response
     */
    public function edit(ExportData $exportData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExportData  $exportData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExportData $exportData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExportData  $exportData
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExportData $exportData)
    {
        //
    }

    public function exportVolByPort(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        $data =  $this->exportDataService->exportVolByPort($filters);

        return Excel::download(new ExportMarketScenarioByPort($data), 'test.xlsx');
    }

    public function exportVolByRegion(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        $data = $this->exportDataService->exportVolByRegion($filters);

        return Excel::download(new RegionWiseExportCounts($data), 'test.xlsx');
    }
}
