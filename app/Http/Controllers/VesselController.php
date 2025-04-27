<?php

namespace App\Http\Controllers;

use App\Exports\OperatorWiseSummary;
use App\Exports\SocInOutBound;
use App\Http\Requests\ImportExportCountCreateRequest;
use App\Http\Requests\VesselInfoCreateRequest;
use App\Http\Requests\VesselInfoUpdateRequest;
use App\Http\Requests\VesselStoreRequest;
use App\Http\Requests\VesselUpdateRequest;
use App\Models\ImportExportCount;
use App\Models\Mlo;
use App\Models\MloWiseCount;
use App\Models\Route;
use App\Models\VesselInfos;
use App\Services\VesselService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;

class VesselController extends Controller
{
    protected $vesselService;

    public function __construct(VesselService $vesselService)
    {
        $this->vesselService = $vesselService;

        // $this->middleware('permission:vessel-list|vessel-create|vessel-edit|vessel-delete', ['only' => ['index', 'show']]);
        // $this->middleware('permission:vessel-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:vessel-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:vessel-delete', ['only' => ['destroy']]);
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

    public function indexVesselInfo(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        
        $data = $this->vesselService->getAllVesselWiseData($filters);
        return view('vessel-infos.index', compact('data'));
    }

    public function createVesselInfo()
    {
        $routes = Route::all();
        return view('vessel-infos.create', compact('routes'));
    }

    public function storeVesselInfo(VesselInfoCreateRequest $request)
    {
        return $this->vesselService->createVesselWiseData($request->validated());
    }

    public function updateVesselInfo(VesselInfoUpdateRequest $request)
    {
        // dd($request->all());
        $vesselInfo = VesselInfos::findOrFail($request->vessel_info_id);
        // dd($request->validated());
        $vesselInfo->update($request->validated());
    }

    public function indexReport()
    {
        return view('reports.index');
    }

    public function operatorWiseLifting(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getAllRoutes();
        $data = $this->vesselService->operatorWiseLifting($filters);
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

        $data = $this->vesselService->operatorWiseLifting($filters);

        $fileName = "OperatorWiseLifting(Summary) - {$range}" . ($route ? " - {$route}" : '') . ".xlsx";

        return Excel::download(new OperatorWiseSummary($data[0], $data[1], $range, $route), $fileName);
    }


    public function socInOutBound(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getData('Route');
        $datas = $this->vesselService->socInOutBound($filters);

        return view('reports.soc-in-out-bound', compact('pods', 'datas'));
    }

    public function socInOutBoundDownload(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            return [];
        }

        // dd($filters);
        $datas = $this->vesselService->socInOutBound($filters);
        $range = Carbon::parse($filters['from_date'])->format('M-y') . ' To ' . Carbon::parse($filters['to_date'])->format('M-y');

        $routeNames = [1 => 'SIN', 2 => 'CBO', 3 => 'CCU'];
        $route = collect($filters['route_id'] ?? [])
            ->map(fn($id) => $routeNames[$id] ?? '')
            ->filter()
            ->implode(', ');

        return Excel::download(new SocInOutBound($datas,$range,$route), 'soc_in_out_bound.xlsx');
    }


    public function vesselTurnAroundTime(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getData('Route');
        $datas = $this->vesselService->vesselTurnAroundTime($filters);


        return view('reports.vessel-turn-around-time', compact('pods', 'datas'));
    }

    public function marketCompetitors(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getData('Route');
        $datas = $this->vesselService->marketCompetitors($filters);

        return view('reports.market-competitors', compact('pods', 'datas'));
    }

    public function socOutboundMarketStrategy(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getData('Route');
        $datas = $this->vesselService->socOutboundMarketStrategy($filters);

        return view('reports.soc-outbound-market', compact('datas', 'pods'));
    }
}
