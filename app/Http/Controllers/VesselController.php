<?php

namespace App\Http\Controllers;

use App\Exports\OperatorWiseSummary;
use App\Http\Requests\ImportExportCountCreateRequest;
use App\Http\Requests\VesselInfoCreateRequest;
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
        $filters = $request->only(['from_date', 'to_date', 'type']);
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
        $range = Carbon::parse($request['from_date'])->format('d-M-y') . ' To ' . Carbon::parse($request['to_date'])->format('d-M-y');
        $data = $this->vesselService->operatorWiseLifting($filters);

        return Excel::download(
            new OperatorWiseSummary($data[0], $data[1], $range),
            'OperatorWiseLifting(Summary).xlsx'
        );
    }
    

    public function socInOutBound(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->vesselService->getData('Route');
        $datas = $this->vesselService->socInOutBound($filters);

        return view('reports.soc-in-out-bound', compact('pods', 'datas'));
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
