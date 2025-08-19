@extends('layouts.app', ['activePage' => 'dashboard', 'title' => 'GLA Admin', 'navName' => 'Dashboard', 'activeButton' => 'laravel'])

@push('styles')
    {{-- @vite('resources/css/pages/dashboard.css') --}}
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="content">
        <div class="container-fluid">
            {{-- <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats bg-warning">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-grid-45 text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="card-category">Total Vessel Call</p>
                                        <h5 id="teus" class="card-title"></h5>
                                        <h4 id="totalVesselCall"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            
                            <div class="stats float-right">
                                <p class="card-title font-weight-bold" id="laden_export_total"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats bg-success">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-2">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-grid-45 text-success"></i>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="numbers">
                                        <p class="card-category">Avg. Anchorage</p>
                                        <h4 id="laden_export_20" class="card-title"></h4>
                                        <h4 id="avgAnchorageTime" class="card-title"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            
                            <div class="stats float-right">
                                <p class="card-title font-weight-bold" id="laden_export_total"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats bg-primary">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-2">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-grid-45 text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <div class="numbers">
                                        <p class="card-category">Avg. Berth Stay</p>
                                        <h4 id="empty_export_20" class="card-title"></h4>
                                        <h4 id="avgBerthTime" class="card-title"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            
                            <div class="stats float-right">
                                <p class="card-title font-weight-bold" id="empty_export_total"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-stats bg-danger">
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="nc-icon nc-grid-45 text-danger"></i>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="card-category">Avg. Turn Around</p>
                                        <h4 class="card-title" id="vessel_count"></h4>
                                        <h4 class="card-title" id="avgTurnAroundTime"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            
                            <div class="stats float-right">
                                <p class="card-title font-weight-bold" id="laden_export_total"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                @php
                    $cards = [
                        [
                            'title' => 'Total Vessel Call',
                            'iconColor' => 'text-warning',
                            'iconBg' => 'bg-warning-subtle',
                            'icon' => 'fas fa-ship',
                            'valueId' => 'totalVesselCall',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'Avg. Anchorage',
                            'iconColor' => 'text-success',
                            'iconBg' => 'bg-success-subtle',
                            'icon' => 'fas fa-anchor',
                            'valueId' => 'avgAnchorageTime',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'Avg. Berth Stay',
                            'iconColor' => 'text-primary',
                            'iconBg' => 'bg-primary-subtle',
                            'icon' => 'fas fa-clock',
                            'valueId' => 'avgBerthTime',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'Avg. Turn Around',
                            'iconColor' => 'text-danger',
                            'iconBg' => 'bg-danger-subtle',
                            'icon' => 'fas fa-sync-alt',
                            'valueId' => 'avgTurnAroundTime',
                            'element' => 'h5'
                        ],

                        [
                            'title' => 'Avg. Box Handling',
                            'iconColor' => 'text-warning',
                            'iconBg' => 'bg-warning-subtle',
                            'icon' => 'fas fa-box-open',
                            'valueId' => 'avgBoxHandling',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'Avg. Import (Teu)',
                            'iconColor' => 'text-success',
                            'iconBg' => 'bg-success-subtle',
                            'icon' => 'fas fa-balance-scale-right',
                            'valueId' => 'avgImportTeu',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'Avg. Export (Teu)',
                            'iconColor' => 'text-primary',
                            'iconBg' => 'bg-primary-subtle',
                            'icon' => 'fas fa-balance-scale-left',
                            'valueId' => 'avgExportTeu',
                            'element' => 'h5'
                        ],
                        [
                            'title' => 'TTL Vessel',
                            'iconColor' => 'text-danger',
                            'iconBg' => 'bg-danger-subtle',
                            'icon' => 'fas fa-ship',
                            'valueId' => 'ttlVesselByCrane',
                            'element' => 'h5'
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm rounded-lg bg-white h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-3 mr-4">
                                    <div class="d-flex align-items-center justify-content-center {{ $card['iconBg'] }} {{ $card['iconColor'] }} rounded-circle shadow-sm"
                                        style="width: 48px; height: 48px;">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 small text-muted fw-semibold">{{ $card['title'] }}</h6>
                                    <h5 id="{{ $card['valueId'] }}" class="fw-bold text-dark mb-0 lh-sm2">--</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>




            {{-- Floating Filter Toggle --}}
            <div id="floating-filter-toggle" class="position-fixed" style="bottom: 20px; right: 20px; z-index: 1050;">
                {{-- <span class="fw-bold text-dark mr-1">Filter</span> --}}
                <button class="btn text-white btn-lg rounded-circle" style="background: #0056b3" onclick="toggleFilter()"
                    title="Filter">
                    {{-- <i class="fa fa-filter"></i> --}}
                    {{-- <i class="fa-solid fa-magnifying-glass fa-beat"></i> --}}
                    {{-- <i class="fa-solid fa-magnifying-glass fa-shake"></i> --}}
                    <i class="fa-solid fa-magnifying-glass fa-beat-fade text-lg"></i>
                </button>
            </div>


            {{-- Filter Panel --}}
            <div id="floating-filter-panel" class="position-fixed shadow-lg bg-white rounded border p-2"
                style="bottom: 80px; right: 20px; z-index: 1050; width: 90vw; max-width: 700px; display: none;">

                {{-- start filter --}}
                <div class="card sticky-filter">
                    {{-- <div class="card-header mb-0 pb-0">
                        <p class="mb-0 pb-0">Filter</p>
                    </div> --}}
                    <div class="card-body bg-light px-1 pb-0 pt-2">
                        <div class="row">
                            {{-- Left Column: Years + Months --}}
                            <div class="col-12 col-lg-4 mb-3 pr-0 mb-lg-0">
                                <div class="card border mb-2">
                                    <div class="card-body p-0">
                                        {{-- Years --}}
                                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                                            @foreach ($years as $year)
                                                <label class="btn btn-outline-primary btn-xs m-1">
                                                    <input type="checkbox" class="filter-checkbox" name="years[]"
                                                        value="{{ $year }}" autocomplete="off">
                                                    {{ $year }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="card border">
                                    <div class="card-body p-0">
                                        {{-- Months --}}
                                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                                            @foreach ($months as $monthId => $monthName)
                                                <label class="btn btn-outline-primary btn-xs m-1">
                                                    <input type="checkbox" class="filter-checkbox" name="months[]"
                                                        value="{{ $monthId }}" autocomplete="off">
                                                    {{ $monthName }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Gear Types + Operators --}}
                            <div class="col-12 col-lg-8">
                                <div class="row">
                                    <div class="col-sm-4 pr-0">
                                        <div class="card border mb-2">
                                            <div class="card-body p-0">
                                                {{-- Gear Type --}}
                                                <div class="btn-group-toggle w-100 flex-wrap d-flex" data-toggle="buttons">
                                                    @foreach (['g' => 'Geared', 'gl' => 'Gearless'] as $val => $label)
                                                        <label class="btn btn-outline-primary btn-xs m-1">
                                                            <input type="checkbox" class="filter-checkbox"
                                                                name="gear_types[]" value="{{ $val }}"
                                                                autocomplete="off">
                                                            {{ $label }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="card border mb-2">
                                            <div class="card-body p-0">
                                                {{-- Gear Type --}}
                                                <div class="btn-group-toggle w-100 flex-wrap d-flex" data-toggle="buttons">
                                                    @foreach ($containerSizes as $sz)
                                                        <label class="btn btn-outline-primary btn-xs m-1">
                                                            <input type="checkbox" class="filter-checkbox"
                                                                {{-- data-subtypes='@json($containerSizes[$val])' --}} name="ctn_sizes[]"
                                                                value="{{ $sz }}" autocomplete="off">
                                                            {{ $sz }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card border">
                                    <div class="card-body p-0 d-flex flex-wrap overflow-auto" style="max-height:65px">
                                        {{-- Operators --}}
                                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                                            @foreach ($operators as $operator)
                                                <label class="btn btn-outline-primary btn-xs m-1">
                                                    <input type="checkbox" class="filter-checkbox" name="operators[]"
                                                        value="{{ $operator }}" autocomplete="off">
                                                    {{ $operator }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end filter --}}
            </div>






            {{-- start import/export container handling --}}
            <div class="row">
                <div class="col-md-6 pr-0 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header ">
                            <h5 class="card-title">{{ __('Import Container Handling (Teus)') }}</h5>
                            {{-- <p class="card-category">{{ __('Monthly Graph') }}</p> --}}
                        </div>
                        <div class="card-body">
                            <div id="importData" class="ct-chart"></div>
                        </div>
                        <div class="card-footer pt-0">
                            <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('Empty Teus') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Laden Teus') }}
                                {{-- <i class="fa fa-circle text-success"></i> {{ __('Total Laden Export') }} --}}
                            </div>
                            {{-- <hr> --}}
                            <div class="stats">
                                {{-- <i class="fa fa-check"></i> {{ __('Data information certified') }} --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header ">
                            <h5 class="card-title">{{ __('Export Container Handling (Teus)') }}</h5>
                            {{-- <p class="card-category">{{ __('Monthly Graph') }}</p> --}}
                        </div>
                        <div class="card-body">
                            <div id="exportData" class="ct-chart"></div>
                        </div>
                        <div class="card-footer pt-0">
                            <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('Empty Teus') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Laden Teus') }}
                            </div>
                            {{-- <hr> --}}
                            <div class="stats">
                                {{-- <i class="fa fa-check"></i> {{ __('Data information certified') }} --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- start commodity teu bar chart --}}
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header ">
                            <h5 class="card-title">{{ __('Commodity Wise Export Volume (Teus)') }}</h5>
                            {{-- <p class="card-category">{{ __('Monthly Graph') }}</p> --}}
                        </div>
                        <div class="card-body">
                            <div id="commodityTeuBarChart" class="ct-chart"></div>
                        </div>
                        <div class="card-footer ">
                            {{-- <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('Empty Teus') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Laden Teus') }}
                            </div>
                            <hr>
                            <div class="stats">
                                <i class="fa fa-check"></i> {{ __('Data information certified') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 pr-0 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Region Wise Export Volume (Teus)') }}</h5>
                        </div>
                        <div class="card-body">
                            {{-- <div style="overflow-x: auto;"> --}}
                            <div id="regionTeuChart" class="ct-chart"></div>
                            {{-- </div> --}}
                        </div>

                        <div class="card-footer">
                            <i class="fa fa-circle text-info"></i> {{ __('Regions') }}
                            <hr class="mt-0">
                            <div>
                                <h6 style="text-align: justify"><b>EU</b>:Europe,<b> FE</b>:FarEast,<b>
                                        AE</b>:ArabianGulf,<b> MED</b>:Mediterranean,<b> AW</b>:AfricaWest,<b>
                                        ISC</b>:IndianSubcontinent,<b> CA</b>:CentralAmerica,<b> MID</b>:MiddleEast,<b>
                                        NZ</b>:NewZealand,<b> AS</b>:SoutheastAsia,<b> AF</b>:Africa,<b>
                                        CIS</b>:CISCountries,<b> PNG</b>:PapuaNewGuinea</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header">
                            <h5 class="card-title">
                                <div class="row">
                                    <div class="col-md-8">{{ __('Export Commodity (%)') }} </div>
                                    <div class="col-md-4">
                                        <select id="commodityVsOtherChart" name="commodityVsOtherChart" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-8 pr-0">
                                    <div id="gmtsPercentageChart" class="ct-chart"></div>
                                </div>
                                <div class="col-sm-4 pl-0">
                                    <div id="gmtsPrecentageChartPODInfoContainer"
                                        class="gmtsPrecentageChartPODInfoContainer"></div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer pt-0">
                            <div class="legend">
                                <i class="fa fa-circle text-danger"></i> <span id="currentCommodity"></span>
                                <i class="fa fa-circle text-info"></i> {{ __('Others') }}
                                {{-- <i class="fa fa-circle text-success"></i> {{ __('Total Laden Export') }} --}}
                            </div>
                            <hr class="m-0">
                            <div id="gmtsPrecentageChartInfoContainer" class="gmtsPrecentageChartInfoContainer"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('SINOKOR EXPORT PORTS (Teus)') }}</h5>
                        </div>
                        <div class="card-body">
                            <div>
                                <div id="snkOtherPODTeuChart" class="ct-chart"></div>
                            </div>
                        </div>
                        <div class="card-footer pt-0">
                            <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('Sinokor') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('Others') }}
                                {{-- <i class="fa fa-circle text-success"></i> {{ __('Total Laden Export') }} --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- mlo commodity pod tue bar chart --}}
            {{-- <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm rounded-lg bg-white">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8"><h5 class="card-title">{{ __('MLO Commodity POD') }} </h5></div>
                                <div class="col-md-4">
                                    <select id="mloComPodChart" name="mloComPodChart" class="form-control form-control-sm">
                                    </select>
                                </div>
                            </div>
                            
                            
                        </div>
                        <div class="card-body">
                                
                                <div class="chart-scroll-wrapper overflow-auto">
                                    <div id="podMLOCommodityTeuBarChart" class="ct-chart"></div>
                                </div>
                        </div>
                        <div class="card-footer">
                            <div class="podMloCommodityTeuBarInfo"></div>
                        </div>
                    </div>
                </div>
            </div> --}}



        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('js/dashboard.js') }}" type="text/javascript"></script>
    <script>
        window.dashboardRoutes = {
            getData: "{{ route('dashboard.get_data') }}"
        };
        window.csrfToken = "{{ csrf_token() }}";

        function toggleFilter() {
            const panel = document.getElementById('floating-filter-panel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }

        // Optional: Hide when clicking outside
        document.addEventListener('click', function(event) {
            const filterPanel = document.getElementById('floating-filter-panel');
            const filterToggle = document.getElementById('floating-filter-toggle');

            if (!filterPanel.contains(event.target) && !filterToggle.contains(event.target)) {
                filterPanel.style.display = 'none';
            }
        });
    </script>
    {{-- @vite('resources/js/pages/dashboard.js') --}}
@endpush
