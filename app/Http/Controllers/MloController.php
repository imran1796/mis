<?php

namespace App\Http\Controllers;

use App\Exports\MLoWiseSummary;
use App\Http\Requests\MloStoreRequest;
use App\Http\Requests\MloUpdateRequest;
use App\Http\Requests\MloWiseCountCreateRequest;
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

    public function getAllMloWisePerMonthRoute(Request $request){
        $filters = $request->only(['date', 'route_id']);
        $mloWisePerMonthRoute = MloWiseCount::select('date', 'route_id')
            ->with('route')
            ->distinct('date')
            ->orderByDesc('date')
            ->whereIn('route_id',$filters['route_id'])
            ->when(!empty($filters['date']), function ($q) use ($filters) {
                $q->whereDate('date', Carbon::parse($filters['date'])->startOfMonth());
            })
            ->get()
            ->groupBy('date');

        return response()->json($mloWisePerMonthRoute);
    }

    public function createMloWiseCount()
    {
        $routes = Route::all();
        // $mloWisePerMonth = MloWiseCount::select('date', 'route_id')
        //     ->with('route')
        //     ->distinct()
        //     ->orderByDesc('date')
        //     ->get()->groupBy('date');
            // ->get()->groupBy(['date', 'route.short_name']);
        // dd($mloWisePerMonth->toArray());

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
        $filters = $request->only(['from_date', 'to_date', 'route_id', 'mlos']);
        $pods = Route::all();

        $results = $this->mloService->mloWiseSummary($filters);
        return view('reports.mlo-wise-summary', compact('pods', 'results'));

        // return Excel::download(new MLoWiseSummary($data,$range), 'mloWiseSummary'.$range.'.xlsx');
    }

    public function socOutboundMarketStrategy(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'route_id']);
        $pods = $this->mloService->getData('Route');
        $datas = $this->mloService->socOutboundMarketStrategy($filters);

        return view('reports.soc-outbound-market', compact('datas', 'pods'));
    }
}
