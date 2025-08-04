<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HomeService;

class HomeController extends Controller
{
    protected $homeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(HomeService $homeService)
    {
        $this->middleware('auth');
        $this->homeService = $homeService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $operators = $this->homeService->getAllUniqueOperators();
        $years = $this->homeService->getUniqueYears();
        $months = $this->homeService->getAllMonths(); 
        $containerSizes = $this->homeService->getAllContainerSizes(); 

        return view('dashboard', compact('operators', 'years', 'months', 'containerSizes'));
    }

    public function getData(Request $request)
    {
        return $this->homeService->getData($request);
    }
}
