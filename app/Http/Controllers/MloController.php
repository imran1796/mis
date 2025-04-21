<?php

namespace App\Http\Controllers;

use App\Exports\MLoWiseSummary;
use App\Http\Requests\MloStoreRequest;
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

        // $this->middleware('permission:mlo-list|mlo-create|mlo-edit|mlo-delete', ['only' => ['index', 'show']]);
        // $this->middleware('permission:mlo-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:mlo-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:mlo-delete', ['only' => ['destroy']]);

        // $this->middleware('permission:mloData-list|mloData-create|mloData-edit|mloData-delete', ['only' => ['indexMloData', 'showMloData']]);
        // $this->middleware('permission:mloData-create', ['only' => ['createMloData', 'storeMloData']]);
        // $this->middleware('permission:mloData-edit', ['only' => ['editMloData', 'updateMloData']]);
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

    public function indexMloWiseCount(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'type', 'mlo', 'pod']);
        $data = $this->mloService->getAllMloWiseCount($filters);
        $pods = $this->mloService->getAllRoutes();
        $mlos = $this->mloService->getAllMlos();
        return view('mlo-wise-counts.index', compact('data', 'mlos', 'pods'));
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

    public function socOutboundMarketStrategy(Request $request)
    {
        $pods = Route::all();

        return view('reports.soc-out-bound-market', compact('pods', 'data'));
    }

    public function mloWiseSummary(Request $request)
    {
        $pods = Route::all();
        $mloWiseDatas = MloWiseCount::with('mlo')
            ->get()->groupBy(['mlo_code', 'date']);

        $results = [];
        foreach ($mloWiseDatas as $mlo => $mloWiseData) {
            // dd($mloWiseData->toArray());
            $results[$mlo] = [
                'totalImportLdnTeus' => 0,
                'totalImportMtyTeus' => 0,
                'totalExportLdnTeus' => 0,
                'totalExportMtyTeus' => 0,
                'mlo' => $mloWiseData->first()->first()->mlo->mlo_code??'',
                'lineBelongsTo' => $mloWiseData->first()->first()->mlo->line_belongs_to??'',
                'mloDetails' => $mloWiseData->first()->first()->mlo->mlo_details??'',
            ];

            foreach ($mloWiseData as $date => $data) {
                $month = Carbon::parse($date)->format('M');

                // Safe defaults
                if (!isset($results[$mlo]['permonth'][$month])) {
                    $results[$mlo]['permonth'][$month] = [
                        'importLdnTeus' => 0,
                        'importMtyTeus' => 0,
                        'exportLdnTeus' => 0,
                        'exportMtyTeus' => 0,
                    ];
                }

                $import = $data[0] ?? null;
                $export = $data[1] ?? null;

                // TEU calculations
                $importLaden = ($import->dc20 ?? 0) + ($import->r20 ?? 0) + ((($import->dc40 ?? 0) + ($import->dc45 ?? 0) + ($import->r40 ?? 0)) * 2);
                $importEmpty = ($import->mty20 ?? 0) + (($import->mty40 ?? 0) * 2);

                $exportLaden = ($export->dc20 ?? 0) + ($export->r20 ?? 0) + ((($export->dc40 ?? 0) + ($export->dc45 ?? 0) + ($export->r40 ?? 0)) * 2);
                $exportEmpty = ($export->mty20 ?? 0) + (($export->mty40 ?? 0) * 2);

                // Store monthly values
                $results[$mlo]['permonth'][$month]['importLdnTeus'] += $importLaden;
                $results[$mlo]['permonth'][$month]['importMtyTeus'] += $importEmpty;
                $results[$mlo]['permonth'][$month]['exportLdnTeus'] += $exportLaden;
                $results[$mlo]['permonth'][$month]['exportMtyTeus'] += $exportEmpty;

                // Store totals
                $results[$mlo]['totalImportLdnTeus'] += $importLaden;
                $results[$mlo]['totalImportMtyTeus'] += $importEmpty;
                $results[$mlo]['totalExportLdnTeus'] += $exportLaden;
                $results[$mlo]['totalExportMtyTeus'] += $exportEmpty;
            }
        }
        // dd($results);
        return view('reports.mlo-wise-summary', compact('pods', 'results'));

        // return Excel::download(new MLoWiseSummary($data,$range), 'mloWiseSummary'.$range.'.xlsx');
    }
}
