<?php

namespace App\Http\Controllers;

use App\Http\Requests\MloStoreRequest;
use App\Services\MloService;
use Illuminate\Http\Request;

class MloController extends Controller
{
    protected $mloService;

    public function __construct(MloService $mloService){
        $this->mloService = $mloService;

        // $this->middleware('permission:mlo-list|mlo-create|mlo-edit|mlo-delete', ['only' => ['index', 'show']]);
        // $this->middleware('permission:mlo-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:mlo-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:mlo-delete', ['only' => ['destroy']]);
    }

    public function index(){
        $mlos = $this->mloService->getAllMlos();
        return view('mlos.index',compact('mlos'));
    }

    public function create(){
        return view('mlos.create');
    }

    public function store(MloStoreRequest $request){
        $mlo = $request->validated();
        return $this->mloService->createMlo($mlo);
    }

    public function mloWiseIndex(){
        return view('mlos.upload');
    }

    // public function mloWiseStore(Request $request){
    //     return $this->mloService->storeMloWisedata($request);
    // }
}
