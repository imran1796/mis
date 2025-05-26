@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'MLO',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-datepicker-calendar {
            display: none;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>MLO NVOCC Summary</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form class="row px-3 mb-2">
                    @php
                        $mlos = [
                            'ALC',
                            'ALL',
                            'ANL',
                            'APL',
                            'ARL',
                            'BCS',
                            'BLPL',
                            'BLS',
                            'BTC',
                            'CLN',
                            'CMA',
                            'COSCO',
                            'CSI',
                            'EGH',
                            'EGL',
                            'EMA',
                            'EMC',
                            'EMS',
                            'EMU',
                            'FSC',
                            'GAP',
                            'GLG',
                            'GSL',
                            'HEUNGA',
                            'HL',
                            'HMM',
                            'IM',
                            'INL',
                            'KCS',
                            'KMTC',
                            'LCN',
                            'LEL',
                            'LSC',
                            'MAXI',
                            'MCC',
                            'MLG',
                            'MLS',
                            'MSC',
                            'MSL',
                            'NGS',
                            'ONE',
                            'OOCL',
                            'OUL',
                            'PIL',
                            'PLO',
                            'PRC',
                            'QCML',
                            'RXL',
                            'SAF',
                            'SAMU',
                            'SAS',
                            'SCC',
                            'SCI',
                            'SETH',
                            'SITARA',
                            'SKN',
                            'SPI',
                            'TIZ',
                            'TPS',
                            'TSM',
                            'TVS',
                            'UCL',
                            'VMP',
                            'VSC',
                            'VSL',
                            'WHL',
                            'YML',
                        ];
                    @endphp
                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" name="route_id[]" class="form-control form-control-sm selectpicker" multiple
                            title="Select Route">
                            @foreach ($pods as $pod)
                                <option value="{{ $pod->id }}"
                                    {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }}>
                                    {{ $pod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 form-group">
                        <select data-live-search="true" data-actions-box="true" id="mlo" name="mlos[]"
                            class="form-control form-control-sm search-select selectpicker" multiple>
                            @foreach ($mlos as $mlo)
                                <option value="{{ $mlo }}" selected>{{ $mlo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input placeholder="From Date" class="form-control form-control-sm datepicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-md-2 mt-1 px-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input placeholder="To Date" class="form-control form-control-sm datepicker" type="text"
                            name="to_date" id="to_date" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2 px-1 mt-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
                    </div>

                    <div class="col-md-2 px-1 mt-1">
                        {{-- <a href="{{ route('reports.soc-inout-bound.download', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download" aria-hidden="true"></i> (xlsx)
                        </a> --}}
                        <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i
                                class="fa fa-download" aria-hidden="true"></i> xls</button>
                    </div>
                </form>

                <div class="card bg-white">
                    <div class="card-header">
                        {{-- start auto search --}}
                        <div class="form-group">
                            <label for="mr" class="sr-only">Auto Search</label>
                            <input value="" type="text" name="autosearch" id="autosearch"
                                class="form-control form-control-sm" placeholder="Auto Search ">
                        </div>
                        {{-- end auto search --}}
                    </div>
                    <div class="card-body">
                        @php
                            $selectedRouteIds = request('route_id', [1, 2, 3]);
                            $selectedRoutes = collect($pods)->whereIn('id', $selectedRouteIds);
                            $shortNames = $selectedRoutes->pluck('short_name')->implode(', ');
                            $perMonthCount = count(collect($results)->first()['permonth'] ?? []);
                            $colspan = $perMonthCount * 4 + 12;

                            $allMonths = collect($results)
                                ->flatMap(fn($dt) => array_keys($dt['permonth']))
                                ->unique()
                                ->sortBy(fn($m) => \Carbon\Carbon::parse($m)->month)
                                ->values();
                        @endphp
                        <table id="excelJsTable" class="tableFixHead table-bordered custom-table-report mb-3">
                            <p class="reportRange" style="display: none;" type="hidden">Date: {{ request('from_date') }} to {{ request('to_date') }}</p>
                            <p class="reportTitle" style="display: none;" type="hidden">Mlo_Wise_Summary</p>
                            <input class="reportRange" type="hidden"
                                value="Date: {{ request('from_date') }} to {{ request('to_date') }}">
                            <input class="reportTitle" type="hidden" value="Mlo_Wise_Summary">
                            <thead>
                                <tr>
                                    <th colspan="{{ $colspan }}" style="font-size: 18px" class="text-center">MLO NVOCC
                                        SUMMARY</th>
                                </tr>

                                @if (request()->filled('from_date') && request()->filled('to_date'))
                                    <tr>
                                        <th colspan="{{ $colspan }}" class="text-center reportRange">
                                            <h6 class="text-center"> Date: {{ request('from_date') }} to
                                                {{ request('to_date') }}</h6>
                                        </th>
                                    </tr>
                                @endif

                                @if ($shortNames)
                                    <tr>
                                        <th colspan="{{ $colspan }}" class="pt-0">
                                            <h6 class="text-center">{{ 'Route: ' . $shortNames }}</h6>
                                        </th>
                                    </tr>
                                @endif

                                <tr>
                                    <th rowspan="3">#SL</th>
                                    <th rowspan="3">MLO Code</th>
                                    <th rowspan="3">Line Belongs To</th>
                                    <th rowspan="3">MLO Details</th>
                                    {{-- @if (!empty($results))
                                        @foreach (collect($results)->first()['permonth'] as $month => $d)
                                            <th colspan="4">{{ $month }}</th>
                                        @endforeach
                                    @endif --}}
                                    @foreach ($allMonths as $month)
                                        <th colspan="4" class="text-center">{{ $month }}</th>
                                    @endforeach

                                    <th colspan="4">Total Teus</th>
                                    <th colspan="4">Average Teus</th>
                                </tr>
                                <tr>
                                    {{-- @if (!empty($results))
                                        @foreach (collect($results)->first()['permonth'] as $i)
                                            <th colspan="2">Import</th>
                                            <th colspan="2">Export</th>
                                        @endforeach
                                    @endif --}}
                                    @foreach ($allMonths as $month)
                                        <th colspan="2">Import</th>
                                        <th colspan="2">Export</th>
                                    @endforeach

                                    <th colspan="2">Import</th>
                                    <th colspan="2">Export</th>
                                    <th colspan="2">Import</th>
                                    <th colspan="2">Export</th>
                                </tr>
                                <tr>
                                    {{-- @if (!empty($results))
                                        @foreach (collect($results)->first()['permonth'] as $i)
                                            <th>LDN</th>
                                            <th>MTY</th>
                                            <th>LDN</th>
                                            <th>MTY</th>
                                        @endforeach
                                    @endif --}}

                                    @foreach ($allMonths as $month)
                                        <th>LDN</th>
                                        <th>MTY</th>
                                        <th>LDN</th>
                                        <th>MTY</th>
                                    @endforeach

                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($results as $mlo => $dt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $mlo }}</td>
                                        <td>{{ $dt['lineBelongsTo'] }}</td>
                                        <td>{{ $dt['mloDetails'] }}</td>

                                        {{-- @foreach ($dt['permonth'] as $month => $t)
                                            <td>{{ $t['importLdnTeus'] }}</td>
                                            <td>{{ $t['importMtyTeus'] }}</td>
                                            <td>{{ $t['exportLdnTeus'] }}</td>
                                            <td>{{ $t['exportMtyTeus'] }}</td>
                                        @endforeach --}}
                                        @foreach ($allMonths as $month)
                                            @php
                                                $data = $dt['permonth'][$month] ?? [
                                                    'importLdnTeus' => 0,
                                                    'importMtyTeus' => 0,
                                                    'exportLdnTeus' => 0,
                                                    'exportMtyTeus' => 0,
                                                ];
                                            @endphp
                                            <td>{{ $data['importLdnTeus'] }}</td>
                                            <td>{{ $data['importMtyTeus'] }}</td>
                                            <td>{{ $data['exportLdnTeus'] }}</td>
                                            <td>{{ $data['exportMtyTeus'] }}</td>
                                        @endforeach

                                        <td>{{ $dt['totalImportLdnTeus'] }}</td>
                                        <td>{{ $dt['totalImportMtyTeus'] }}</td>
                                        <td>{{ $dt['totalExportLdnTeus'] }}</td>
                                        <td>{{ $dt['totalExportMtyTeus'] }}</td>

                                        <td>{{ round($dt['totalImportLdnTeus'] / count($dt['permonth'])) }}</td>
                                        <td>{{ round($dt['totalImportMtyTeus'] / count($dt['permonth'])) }}</td>
                                        <td>{{ round($dt['totalExportLdnTeus'] / count($dt['permonth'])) }}</td>
                                        <td>{{ round($dt['totalExportMtyTeus'] / count($dt['permonth'])) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            initializeMonthYearPicker('.datepicker');

            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('.selectpicker').selectpicker({
                actionsBox: true,
                deselectAllText: 'Deselect All',
                countSelectedText: (numSelected) =>
                    numSelected === 1 ? "{0} item selected" : "{0} items selected",
                selectedTextFormat: 'count > 3'
            });

            $('.selectpicker').on('loaded.bs.select', function() {
                $(this).parent().find('.bs-select-all').hide();
            });
        });

        function initializeMonthYearPicker(selector) {
            $(selector).datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'M-yy',

                onChangeMonthYear: function(year, month, inst) {
                    $(this).datepicker('setDate', new Date(year, month - 1, 1));
                },

                onClose: function() {
                    const iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    const iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();


                    if (iMonth !== null && iYear !== null) {
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                    }
                },

                beforeShow: function() {
                    const selDate = $(this).val();
                    if (selDate.length > 0) {
                        const iYear = selDate.slice(-4);
                        const iMonth = $.inArray(selDate.slice(0, -5), $(this).datepicker('option',
                            'monthNames'));

                        if (iMonth !== -1) {
                            $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        }
                    }
                }
            });
        }
    </script>
@endpush
