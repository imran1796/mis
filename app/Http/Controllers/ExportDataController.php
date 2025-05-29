<?php

namespace App\Http\Controllers;

use App\Exports\ExportMarketScenarioByPort;
use App\Exports\RegionWiseExportCounts;
use App\Models\ExportData;
use App\Services\ExportDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportDataController extends Controller
{
    protected $exportDataService;

    public function __construct(ExportDataService $exportDataService)
    {
        $this->exportDataService = $exportDataService;

        $this->middleware(
            'permission:exportData-list',
            ['only' => [
                'index',
            ]]
        );

        $this->middleware(
            'permission:exportData-create',
            ['only' => [
                'create',
                'store',
            ]]
        );

        $this->middleware(
            'permission:exportData-delete',
            ['only' => [
                'deleteByDate',
            ]]
        );

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'commodity', 'mlo', 'pod', 'region', 'report_type']);
        $exportDatas = $this->exportDataService->getAllExportData($filters);
        $commodities = $this->exportDataService->getUniqueExportData('commodity');
        $mlos = $this->exportDataService->getUniqueExportData('mlo');
        $pods = $this->exportDataService->getUniqueExportData('pod');
        $regions = $this->exportDataService->getUniqueExportData('trade');
        return view('export-data.index', compact('exportDatas', 'commodities', 'mlos', 'pods', 'regions'));
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
        $exportDataMonthly = ExportData::select('date')
            ->distinct()
            ->orderByDesc('date')
            ->paginate(5);
        return view('export-data.create', compact('exportDataMonthly'));
    }

    public function getUniqueMonths(Request $request){
        return $this->exportDataService->getUniqueExportData('date', $request->year);
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

    public function deleteByDate($date)
    {
        try {
            // dd($date);
            \DB::beginTransaction();

            ExportData::whereDate('date', $date)->delete();

            \DB::commit();

            return response()->json([
                'success' => 'Data deleted successfully for ' . \Carbon\Carbon::parse($date)->format('F Y')
            ]);
        } catch (\Exception $e) {
            // dd($date);
            \DB::rollBack();
            \Log::error('ExportData Deletion Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to delete data. Please try again later.'
            ], 500);
        }
    }


    public function exportVolByPort(Request $request)
    {
        $range = ($request['from_date'] ? Carbon::parse($request['from_date'])->format('M-y') : 'up') .
            ' to ' . Carbon::parse($request['to_date'])->format('M-y');
        $filters = $request->only(['from_date', 'to_date']);
        $data =  $this->exportDataService->exportVolByPort($filters);

        return Excel::download(new ExportMarketScenarioByPort($data, $range), 'exportMarketScenario' . $range . '.xlsx');
    }

    public function exportVolByRegion(Request $request)
    {
        // dd($request->from_date);
        $range = ($request['from_date'] ? Carbon::parse($request['from_date'])->format('M-y') : 'up') .
            ' to ' . Carbon::parse($request['to_date'])->format('M-y');

        $filters = $request->only(['from_date', 'to_date']);
        $data = $this->exportDataService->exportVolByRegion($filters);

        return Excel::download(new RegionWiseExportCounts($data, $range), 'exportVolByRegion' . $range . '.xlsx');
    }
}
