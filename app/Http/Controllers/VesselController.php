<?php

namespace App\Http\Controllers;

use App\Http\Requests\VesselStoreRequest;
use App\Http\Requests\VesselUpdateRequest;
use App\Services\VesselService;
use Illuminate\Http\Request;

class VesselController extends Controller
{
    protected $vesselService;

    public function __construct(VesselService $vesselService){
        $this->vesselService = $vesselService;

        // $this->middleware('permission:vessel-list|vessel-create|vessel-edit|vessel-delete', ['only' => ['index', 'show']]);
        // $this->middleware('permission:vessel-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:vessel-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:vessel-delete', ['only' => ['destroy']]);
    }

    public function index(){
        $vessels = $this->vesselService->getAllVessels();
        return view('vessels.index',compact('vessels'));
    }

    public function create(){
        return view('vessels.create');
    }

    public function store(VesselStoreRequest $request){
        $vessel = $request->validated();
        return $this->vesselService->createVessel($vessel);
    }
}
