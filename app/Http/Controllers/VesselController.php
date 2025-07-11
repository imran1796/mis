<?php

namespace App\Http\Controllers;

use App\Exports\MLOWiseContainerHandling;
use App\Exports\OperatorWiseSummary;
use App\Exports\SocInOutBound;
use App\Exports\VesselOperatorWiseContainerHandling;
use App\Http\Requests\ImportExportCountCreateRequest;
use App\Http\Requests\VesselInfoCreateRequest;
use App\Http\Requests\VesselInfoDeleteByDateRouteRequest;
use App\Http\Requests\VesselInfoDeleteRequest;
use App\Http\Requests\VesselInfoUpdateRequest;
use App\Http\Requests\VesselStoreRequest;
use App\Http\Requests\VesselTurnAroundStoreRequest;
use App\Http\Requests\VesselUpdateRequest;
use App\Models\ImportExportCount;
use App\Models\Mlo;
use App\Models\MloWiseCount;
use App\Models\Route;
use App\Models\VesselInfos;
use App\Models\VesselTurnAround;
use App\Services\VesselInfoService;
use App\Services\VesselService;
use App\Services\VesselTurnAroundService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;

use function Ramsey\Uuid\v1;

class VesselController extends Controller
{
    protected $vesselService, $vesselInfoService, $vesselTurnAroundService;

    public function __construct(VesselService $vesselService, VesselInfoService $vesselInfoService, VesselTurnAroundService $vesselTurnAroundService)
    {
        $this->vesselService = $vesselService;
        $this->vesselInfoService = $vesselInfoService;
        $this->vesselTurnAroundService = $vesselTurnAroundService;

        $this->middleware('permission:vessel-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:vessel-create', ['only' => ['create', 'store' ,'update', 'edit']]);
        $this->middleware('permission:vessel-delete', ['only' => ['destroy']]);

        $this->middleware('permission:operatorData-list', ['only' => ['indexVesselInfo']]);
        $this->middleware('permission:operatorData-create', ['only' => ['createVesselInfo', 'storeVesselInfo', 'updateVesselInfo']]);
        $this->middleware('permission:operatorData-delete', ['only' => ['deletVesselInfoByDateRoute']]);

        $this->middleware('permission:reports', ['only' => ['indexReport']]);

        $this->middleware('permission:turnAround-list', ['only' => ['indexVesselTurnAround']]);
        $this->middleware('permission:turnAround-create', ['only' => ['createVesselTurnAround', 'storeVesselTurnAround']]);
        $this->middleware('permission:turnAround-delete', ['only' => ['deletVesselTurnAroundByDate']]);
    }

    public function index()
    {
        $vessels = $this->vesselService->getAllVessels();
        return view('vessels.index', compact('vessels'));
    }

    public function create()
    {
        return view('vessels.create');
    }

    public function store(VesselStoreRequest $request)
    {
        $vessel = $request->validated();
        return $this->vesselService->createVessel($vessel);
    }

    public function update(VesselUpdateRequest $request){
        // dd($request->all());
        $vessel = $request->validated();
        return $this->vesselService->updateVessel($vessel);
    }
    
    public function delete(){}

    public function getDistinctVesselInfoDates(Request $request){
        $filters = $request->only(['from_date','to_date', 'route_id']);
        $vesselWisePerMonth = $this->vesselInfoService->getDistinctVesselInfoDates($filters);

        return response()->json($vesselWisePerMonth);
    }

    public function indexVesselInfo(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = Route::all();
        $data = $this->vesselInfoService->getAllVesselWiseData($filters);
        return view('vessel-infos.index', compact('data','pods'));
    }

    public function createVesselInfo(Request $request)
    {
        $routes = Route::all();
        return view('vessel-infos.create', compact('routes'));
    }

    public function storeVesselInfo(VesselInfoCreateRequest $request)
    {
        return $this->vesselInfoService->createVesselWiseData($request->validated());
    }

    public function updateVesselInfo(VesselInfoUpdateRequest $request)
    {
        $vesselInfo = VesselInfos::findOrFail($request->vessel_info_id);
        $vesselInfo->update($request->validated());
    }

    public function deletVesselInfoByDateRoute(VesselInfoDeleteByDateRouteRequest $request)
    {
        $filters = $request->only(['date','route_id']);
        return $this->vesselInfoService->deletVesselInfoByDateRoute($filters);
        
    }

    public function indexReport()
    {
        return view('reports.index');
    }

    public function operatorWiseLifting(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselInfoService->getAllRoutes();
        $data = $this->vesselInfoService->operatorWiseLifting($filters);
        // dd($data);
        return view('reports.operator-wise-lifting', compact('pods', 'data'));
    }

    public function operatorWiseLiftingDownload(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);

        if (empty($filters['from_date']) || empty($filters['to_date']) || empty($filters['route_id'])) {
            return redirect()->back();
        }

        $range = Carbon::parse($filters['from_date'])->format('M-y') . ' To ' . Carbon::parse($filters['to_date'])->format('M-y');

        $routeNames = [1 => 'SIN', 2 => 'CBO', 3 => 'CCU'];
        $route = collect($filters['route_id'] ?? [])
            ->map(fn($id) => $routeNames[$id] ?? '')
            ->filter()
            ->implode(', ');

        $data = $this->vesselInfoService->operatorWiseLifting($filters);

        $fileName = "OperatorWiseLifting(Summary) - {$range}" . ($route ? " - {$route}" : '') . ".xlsx";

        return Excel::download(new OperatorWiseSummary($data[0], $data[1], $range, $route), $fileName);
    }

    public function socInOutBound(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselInfoService->getData('Route');
        $datas = $this->vesselInfoService->socInOutBound($filters);

        return view('reports.soc-in-out-bound', compact('pods', 'datas'));
    }

    public function socInOutBoundDownload(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            return [];
        }

        // dd($filters);
        $datas = $this->vesselInfoService->socInOutBound($filters);
        $range = Carbon::parse($filters['from_date'])->format('M-y') . ' To ' . Carbon::parse($filters['to_date'])->format('M-y');

        $routeNames = [1 => 'SIN', 2 => 'CBO', 3 => 'CCU'];
        $route = collect($filters['route_id'] ?? [])
            ->map(fn($id) => $routeNames[$id] ?? '')
            ->filter()
            ->implode(', ');

        return Excel::download(new SocInOutBound($datas, $range, $route), 'soc_in_out_bound.xlsx');
    }


    public function vesselTurnAroundTime(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselInfoService->getData('Route');
        $datas = $this->vesselInfoService->vesselTurnAroundTime($filters);

        return view('reports.vessel-turn-around-time', compact('pods', 'datas'));
    }

    public function marketCompetitors(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id', 'operators']);
        $pods = $this->vesselInfoService->getData('Route');
        $datas = $this->vesselInfoService->marketCompetitors($filters);

        return view('reports.market-competitors', compact('pods', 'datas'));
    }

    public function vesselInfoReport(Request $request)
    {
        $routes = Route::all();
        $operators = $this->vesselInfoService->getAllUniqueOperators();
        $data = $this->vesselInfoService->vesselInfoReport($request);

        return view('reports.vessel-info', compact('data', 'routes', 'operators'));
    }


    /*
        Vessel Turn Around
    */
    public function indexVesselTurnAround(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);

        $data = $this->vesselTurnAroundService->getAllVesselTurnArounds($filters);
        return view('vessel-turn-arounds.index', compact('data'));
    }

    public function getDistinctTurnAroundDates(Request $request){
        $filters = $request->only(['year']);
        $vesselTurnAroundPerMonth = $this->vesselTurnAroundService->getDistinctVesselTurnAroundDates($filters);
        return response()->json($vesselTurnAroundPerMonth);
    }

    public function createVesselTurnAround()
    {
        $routes = Route::all();
        return view('vessel-turn-arounds.create', compact('routes'));
    }

    public function storeVesselTurnAround(VesselTurnAroundStoreRequest $request)
    {
        return $this->vesselTurnAroundService->createVesselTurnAround($request->validated());
    } 

    public function deletVesselTurnAroundByDate(Request $request){
        return VesselTurnAround::whereDate('date', $request->date)->delete();
    }

    public function vesselOperatorWiseLifting(Request $request){
        $pods = $this->vesselInfoService->getAllRoutes();
        $data = $this->vesselInfoService->operatorWiseContainerHandling($request);
        return view('reports.vessel-operator-wise-lifting',compact('data','pods'));
    }
    
    public function operatorWiseContainerHandling(Request $request)
    {
        // dd($request->all());
        $range = Carbon::parse($request['from_date'])->format('M-y').' To '.Carbon::parse($request['to_date'])->format('M-y');
        $routeNames = [1 => 'Singapore', 2 => 'Colombo', 3 => 'Kolkata'];
        $route = collect(request('route_id'))
        ->map(fn($id) => $routeNames[$id] ?? '')
        ->filter()
        ->join(', ');
        $fileName = "Vessel_Operator_Wise_Container_Handling - {$range}" . ($route ? " - {$route}" : '') . ".xlsx";

        $data = $this->vesselInfoService->operatorWiseContainerHandling($request);
        return Excel::download(new VesselOperatorWiseContainerHandling($data,$route,$range), $fileName);
    }
}
