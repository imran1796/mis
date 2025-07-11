<?php

namespace App\Http\Controllers;

use App\Exports\MLOWiseContainerHandling;
use App\Exports\MLoWiseSummary;
use App\Http\Requests\MloStoreRequest;
use App\Http\Requests\MloUpdateRequest;
use App\Http\Requests\MloWiseCountCreateRequest;
use App\Models\Mlo;
use App\Models\MloWiseCount;
use App\Models\Route;
use App\Services\MloService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MloController extends Controller
{
    protected $mloService;

    public function __construct(MloService $mloService)
    {
        $this->mloService = $mloService;

        $this->middleware('permission:mlo-list', ['only' => ['index', 'create', 'store', 'update']]);

        $this->middleware('permission:mloData-list', ['only' => ['indexMloWiseCount']]);
        $this->middleware('permission:mloData-create', ['only' => ['createMloWiseCount', 'storeMloWiseCount']]);
        $this->middleware('permission:mloData-delete', ['only' => ['deleteMloWiseCountByDateRoute']]);
    }

    public function index()
    {
        $mlos = $this->mloService->getAllMlos();
        return view('mlos.index', compact('mlos'));
    }

    public function create()
    {
        return view('mlos.create');
    }

    public function store(MloStoreRequest $request)
    {
        $mlo = $request->validated();
        return $this->mloService->createMlo($mlo);
    }

    public function update(MloUpdateRequest $request, $id)
    {
        $mlo = $request->validated();
        return $this->mloService->updateMlo($id,$mlo);
    }

    public function indexMloWiseCount(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'type', 'mlo', 'pod']);
        $data = $this->mloService->getAllMloWiseCount($filters);
        $pods = $this->mloService->getAllRoutes();
        $mlos = $this->mloService->getAllMlos();
        return view('mlo-wise-counts.index', compact('data', 'mlos', 'pods'));
    }

    public function getDistinctMloWiseDates(Request $request){
        $filters = $request->only(['from_date','to_date', 'route_id']);
        $mloWisePerMonthRoute = $this->mloService->getDistinctMloWiseDates($filters);
        return response()->json($mloWisePerMonthRoute);
    }

    public function createMloWiseCount()
    {
        $routes = Route::all();

        return view('mlo-wise-counts.create', compact('routes'));
    }

    public function storeMloWiseCount(MloWiseCountCreateRequest $request)
    {
        return $this->mloService->createMloWiseCount($request->validated());
    }

    public function deleteMloWiseCountByDateRoute(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'route_id' => 'required|integer|exists:routes,id',
        ]);
        return $this->mloService->deleteMloWiseCountByDateRoute($request);
    }

    public function mloWiseSummary(Request $request)
    {
        // dd($request->all());
        $filters = $request->only(['from_date', 'to_date', 'route_id', 'mlos']);
        $pods = Route::all();
        $mlos = Mlo::select('mlo_code','is_show_nvocc')->get();
        // $selectedMlos = $request->input('mlos');
        // if (!$selectedMlos) {
        //     $selectedMlos = $mlos->where('is_show_nvocc', 1)->pluck('mlo_code')->toArray();
        // }

        $results = $this->mloService->mloWiseSummary($filters);
        return view('reports.mlo-wise-summary', compact('pods', 'results','mlos'));

        // return Excel::download(new MLoWiseSummary($data,$range), 'mloWiseSummary'.$range.'.xlsx');
    }

    public function socOutboundMarketStrategy(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->mloService->getData('Route');
        $datas = $this->mloService->socOutboundMarketStrategy($filters);

        return view('reports.soc-outbound-market', compact('datas', 'pods'));
    }

    public function mloWiseContainerHandling(Request $request){
        $pods = Route::all();
        $data = $this->mloService->mloWiseContainerHandling($request);
        return view('reports.mlo-wise-handling',compact('pods','data'));
    }
    
    public function mloWiseContainerHandlingDownload(Request $request)
    {
        $range = Carbon::parse($request['from_date'])->format('M-y').' To '.Carbon::parse($request['to_date'])->format('M-y');
        $routeNames = [1 => 'Singapore', 2 => 'Colombo', 3 => 'Kolkata'];
        $route = collect(request('route_id'))
        ->map(fn($id) => $routeNames[$id] ?? '')
        ->filter()
        ->join(', ');
        $fileName = "MLO_Wise_Container_Handling - {$range}" . ($route ? " - {$route}" : '') . ".xlsx";
        $data = $this->mloService->mloWiseContainerHandling($request);
        return Excel::download(new MLOWiseContainerHandling($data,$route,$range), $fileName);
    }
}
