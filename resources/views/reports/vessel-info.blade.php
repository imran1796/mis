@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Vessel Info',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-timepicker-container {
            z-index: 9999 !important;
        }

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
                            <h2>Vessel-Operator Info Report</h2>
                        </div>


                    </div>
                </div>

                <form class="row">
                    {{-- Report Type --}}
                    <div class="col-sm-2 pr-0 form-group">
                        <select name="report_type" class="form-control form-control-sm selectpicker" title="Report Type">
                            @foreach (['vessel-wise', 'operator-wise', 'operator-route-wise', 'route-wise'] as $type)
                                <option value="{{ $type }}" {{ request('report_type') == $type ? 'selected' : '' }}>
                                    {{ $type =='vessel-wise'? 'All' : strtoupper(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- From Month --}}
                    <div class="col-sm-2 pr-0 mt-1 form-group">
                        <input type="text" name="from_date" class="form-control form-control-sm monthpicker"
                            placeholder="From Month" value="{{ request('from_date') }}">
                    </div>

                    {{-- To Month --}}
                    <div class="col-sm-2 pr-0 mt-1 form-group">
                        <input type="text" name="to_date" class="form-control form-control-sm monthpicker"
                            placeholder="To Month" value="{{ request('to_date') }}">
                    </div>

                    {{-- Operator --}}
                    <div class="col-sm-2 pr-0  form-group">
                        {{-- <select name="operator[]" class="form-control form-control-sm selectpicker" title="Operators" multiple>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}"
                                    {{ collect(request('operator_id'))->contains($operator->id) ? 'selected' : '' }}>
                                    {{ $operator->name }}
                                </option>
                            @endforeach
                        </select> --}}
                        <select data-live-search="true"
                            class="form-control selectpicker form-control-sm search-select selectpicker" name="operator[]"
                            id="operator" multiple title="Operators">
                            @foreach ($operators as $operator)
                                <option value="{{ $operator }}"
                                    {{ is_array(request('operator')) && in_array($operator, request('operator')) ? 'selected' : '' }}>
                                    {{ $operator }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Route --}}
                    <div class="col-sm-2 pr-0  form-group">
                        <select name="route_id[]" class="form-control form-control-sm selectpicker" title="Route" multiple>
                            @foreach ($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ collect(request('route_id'))->contains($route->id) ? 'selected' : '' }}>
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Shipment Type --}}
                    <div class="col-sm-2 pr-0  form-group">
                        <select name="shipment_type[]" class="form-control form-control-sm selectpicker"
                            title="Export/Import" multiple>
                            @foreach (['export', 'import'] as $type)
                                <option value="{{ $type }}"
                                    {{ collect(request('shipment_type'))->contains($type) ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Container Type --}}
                    <div class="col-sm-2 form-group">
                        <select id="ctnTypeSelect" name="ctn_type[]" class="form-control form-control-sm selectpicker"
                            title="Mty/Ldn" multiple>
                            @foreach (['empty', 'laden'] as $type)
                                <option value="{{ $type }}"
                                    {{ collect(request('ctn_type'))->contains($type) ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Container Size --}}
                    <div class="col-sm-2 form-group">
                        <select id="ctnSizeSelect" name="ctn_size[]" class="form-control form-control-sm selectpicker"
                            title="CTN Size" multiple>
                            @foreach (['dc20', 'dc40', 'dc45', 'r20', 'r40', 'mty20', 'mty40'] as $type)
                                <option value="{{ $type }}"
                                    data-ctn-type="{{ in_array($type, ['mty20', 'mty40']) ? 'empty' : 'laden' }}"
                                    {{ request('ctn_size') != null ? (in_array($type, request('ctn_size')) ? 'selected' : '') : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Search Button --}}
                    <div class="col-sm-1 mt-1 form-group">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
                    </div>

                    <div class="col-sm-1 mt-1 form-group">
                        <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i
                                class="fa fa-download" aria-hidden="true"></i> xls</button>
                    </div>

                    {{-- Optional Export Button --}}
                    {{-- <div class="col-sm-1 mt-1 form-group">
                        <button type="button" id="btnExcelExport" class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download"></i> Excel
                        </button>
                    </div> --}}
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
                            $ctnSizes = ['dc20', 'dc40', 'dc45', 'r20', 'r40', 'mty20', 'mty40'];
                            $headersCtnSizes = array_intersect($ctnSizes, request('ctn_size', $ctnSizes));
                            $headersUnitTeusP = ['unit', 'teus'];
                            $headersExtra = [
                                'teus%',
                                count(array_intersect(['empty', 'laden'], (array) request('ctn_type'))) === 2 ||
                                empty(request('ctn_type'))
                                    ? 'existing/ttl%'
                                    : (request('ctn_type')[0] === 'empty'
                                        ? 'empty/ttlMTY%'
                                        : 'laden/ttlLDN%'),
                            ];
                            $headers = array_merge($headersCtnSizes, $headersUnitTeusP, $headersExtra);
                            $headersCtnUTP = array_merge($headersCtnSizes, $headersUnitTeusP);
                            $types = request('shipment_type', ['export', 'import']);
                            $reportType = request('report_type');
                            $selectedRouteIds = request('route_id', [1, 2, 3]);
                            $selectedRoutes = collect($routes)->whereIn('id', $selectedRouteIds);
                            $shortNames = $selectedRoutes->pluck('short_name')->implode(', ');
                            $colspan = match ($reportType) {
                                'operator-route-wise' => 3,
                                'operator-wise', 'route-wise' => 2,
                                'vessel-wise' => 3,
                                default => 2,
                            };
                        @endphp

                        <table id="excelJsTable"
                            class="tableFixHead table-bordered table2excel custom-table-report mb-3 table-sm">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th colspan="{{ $colspan + count($headers) }}" class="text-center">
                                        <h6 class="m-0 reportTitle text-center" style="font-size: 18px">
                                            <strong>
                                                {{ request()->filled('report_type')
                                                    ? Str::title(str_replace('_', ' ', request('report_type'))) . ' Report'
                                                    : 'Operator Wise Report' }}
                                            </strong>
                                        </h6>
                                    </th>
                                </tr>

                                @if (request()->filled('from_date') && request()->filled('to_date'))
                                    <tr>
                                        <th class="text-center reportRange" colspan="{{ $colspan + count($headers) }}">
                                            Date: {{ request('from_date') }} to {{ request('to_date') }}
                                        </th>
                                    </tr>
                                @endif

                                @if ($shortNames)
                                    <tr>
                                        <th colspan="{{ $colspan + count($headers) }}">
                                            <h6 class="text-center">{{ 'Route: ' . $shortNames }}</h6>
                                        </th>
                                    </tr>
                                @endif

                                <tr>
                                    @if ($reportType === 'operator-route-wise')
                                        <th>Operator</th>
                                        <th>Route</th>
                                    @elseif($reportType === 'operator-wise')
                                        <th>Operator</th>
                                    @elseif($reportType === 'route-wise')
                                        <th>Route</th>
                                    @elseif($reportType === 'vessel-wise')
                                        <th>Vessel</th>
                                        <th>Operator</th>
                                    @else
                                        <th>Operator</th>
                                    @endif
                                    <th>Type</th>
                                    @foreach ($headers as $h)
                                        <th class="text-right">{{ strtoupper($h) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- SKN => SIN/CBO => export/IMPORT => data --}}
                                @if (isset($data[0]))
                                    @foreach ($data[0] as $key1 => $value1)
                                        @if ($reportType === 'operator-route-wise')
                                            {{-- operator=>route=>type=> actual data --}}
                                            @php $routeCount = count($value1); @endphp
                                            @foreach ($value1 as $route => $typeData)
                                                @foreach ($types as $i => $type)
                                                    <tr>
                                                        @if ($loop->parent->first && $i === 0)
                                                            <td rowspan="{{ $routeCount * count($types) }}">
                                                                {{ $key1 }}</td>
                                                        @endif
                                                        @if ($i === 0)
                                                            <td rowspan="{{ count($types) }}">{{ $route }}</td>
                                                        @endif
                                                        <td>{{ strtoupper($type) }}</td>

                                                        {{-- titles --}}
                                                        @foreach ($headersCtnUTP as $h)
                                                            <td class="text-right">
                                                                {{ number_format($typeData[$type][$h] ?? 0) }}</td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ round(($typeData[$type]['teus'] / $data[1]) * 100, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $ctnTypes = (array) request('ctn_type');
                                                            @endphp

                                                            @if (in_array('laden', $ctnTypes) && in_array('empty', $ctnTypes))
                                                                {{ $typeData[$type]['ttlExisting'] }}
                                                            @elseif (in_array('laden', $ctnTypes))
                                                                {{ $typeData[$type]['ladenLaden'] }}
                                                            @elseif (in_array('empty', $ctnTypes))
                                                                {{ $typeData[$type]['emptyEmpty'] }}
                                                            @else
                                                                {{ $typeData[$type]['ttlExisting'] }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @elseif ($reportType === 'vessel-wise')
                                            @php $routeCount = count($value1); @endphp
                                            @foreach ($value1 as $vls => $typeData)
                                                @foreach ($types as $i => $type)
                                                    <tr>
                                                        
                                                        @if ($i === 0)
                                                            <td rowspan="{{ count($types) }}">{{ $vls }}</td>
                                                        @endif
                                                        @if ($i === 0)
                                                            <td rowspan="{{ count($types) }}">{{ $key1 }}</td>
                                                        @endif
                                                        <td>{{ strtoupper($type) }}</td>

                                                        {{-- titles --}}
                                                        @foreach ($headersCtnUTP as $h)
                                                            <td class="text-right">
                                                                {{ number_format($typeData[$type][$h] ?? 0) }}</td>
                                                        @endforeach
                                                        <td class="text-right">
                                                            {{ round(($typeData[$type]['teus'] / $data[1]) * 100, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $ctnTypes = (array) request('ctn_type');
                                                            @endphp

                                                            @if (in_array('laden', $ctnTypes) && in_array('empty', $ctnTypes))
                                                                {{ $typeData[$type]['ttlExisting'] }}
                                                            @elseif (in_array('laden', $ctnTypes))
                                                                {{ $typeData[$type]['ladenLaden'] }}
                                                            @elseif (in_array('empty', $ctnTypes))
                                                                {{ $typeData[$type]['emptyEmpty'] }}
                                                            @else
                                                                {{ $typeData[$type]['ttlExisting'] }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            {{-- @endforeach --}}
                                        @else
                                            @foreach ($types as $i => $type)
                                                {{-- export/import --}}
                                                <tr>
                                                    @if ($i === 0)
                                                        <td rowspan="{{ count($types) }}">{{ $key1 }}</td>
                                                    @endif
                                                    <td>{{ strtoupper($type) }}</td>
                                                    @foreach ($headersCtnUTP as $h)
                                                        <td class="text-right">{{ number_format($value1[$type][$h] ?? 0) }}
                                                        </td>
                                                    @endforeach
                                                    <td class="text-right">
                                                        {{-- value1[export or import] --}}
                                                        {{ round(($value1[$type]['teus'] / $data[1]) * 100, 2) }}</td>
                                                    {{-- <td class="text-right">
                                                        {{ request('ctn_type') == 'laden' ? $value1[$type]['ladenTotal'] : $value1[$type]['emptyTotal'] }}
                                                    </td> --}}
                                                    <td class="text-right">
                                                        @php
                                                            $ctnTypes = (array) request('ctn_type');
                                                        @endphp

                                                        @if (in_array('laden', $ctnTypes) && in_array('empty', $ctnTypes))
                                                            {{ $value1[$type]['ttlExisting'] }}
                                                        @elseif (in_array('laden', $ctnTypes))
                                                            {{ $value1[$type]['ladenLaden'] }}
                                                        @elseif (in_array('empty', $ctnTypes))
                                                            {{ $value1[$type]['emptyEmpty'] }}
                                                        @else
                                                            {{ $value1[$type]['ttlExisting'] }}
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- {{ dd(collect($data[0])->flatten(2)) }} --}}
                                    <td class="text-center" colspan="{{ $colspan }}">Total</td>
                                    @foreach ($headersCtnSizes as $type)
                                        <th class="text-right">
                                            @if (isset($data[0]))
                                                @if ($reportType === 'operator-route-wise' || $reportType === 'vessel-wise')
                                                    {{ collect($data[0])->flatten(2)->sum(function ($item) use ($type) {
                                                            return $item[$type] ?? 0;
                                                        }) }}
                                                @else
                                                    {{ collect($data[0])->flatten(1)->sum(function ($item) use ($type) {
                                                            return $item[$type] ?? 0;
                                                        }) }}
                                                @endif
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="text-right">
                                        @if ($reportType === 'operator-route-wise' || $reportType === 'vessel-wise')
                                            {{ collect($data[0] ?? collect())->flatten(2)->sum('unit') }}
                                        @else
                                            {{ collect($data[0] ?? collect())->flatten(1)->sum('unit') }}
                                        @endif
                                        {{-- {{ collect($data[0] ?? collect())->flatten(1)->sum('unit') }} --}}
                                    </th>
                                    <th class="text-right">
                                        @if ($reportType === 'operator-route-wise' || $reportType === 'vessel-wise')
                                            {{ collect($data[0] ?? collect())->flatten(2)->sum('teus') }}
                                        @else
                                            {{ collect($data[0] ?? collect())->flatten(1)->sum('teus') }}
                                        @endif
                                        {{-- {{ collect($data[0] ?? collect())->flatten(1)->sum('teus') }} --}}
                                    </th>
                                    <th class="text-right">100</th>
                                    {{-- <th></th> --}}
                                    <th></th>
                                </tr>
                            </tfoot>
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
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            initializeMonthYearPicker('.monthpicker');

            $('.selectpicker').selectpicker({
                actionsBox: true,
                deselectAllText: 'Deselect All',
                selectAllText: 'Select All',
                countSelectedText: function(e, t) {
                    return 1 == e ? "{0} item selected" : "{0} items selected"
                },
                selectedTextFormat: 'count'
            });

            function filterCtnSizes() {
                const selectedTypes = $('#ctnTypeSelect').val(); //laden/empty
                const $ctnSizeSelect = $('#ctnSizeSelect');

                $ctnSizeSelect.find('option').each(function() {
                    const ctnType = $(this).data('ctn-type'); //mty/laden
                    const shouldShow =
                        selectedTypes && selectedTypes.length > 0 ?
                        selectedTypes.includes(ctnType) :
                        true;

                    $(this).prop('hidden', !shouldShow);
                });

                if (selectedTypes && selectedTypes.length > 0) {
                    const toSelect = $ctnSizeSelect.find('option').filter(':not([hidden])').map(function() {
                        return this.value;
                    }).get();
                    $ctnSizeSelect.val(toSelect);
                } else {
                    $ctnSizeSelect.val([]);
                }
                $ctnSizeSelect.selectpicker('refresh');
            }
            $('#ctnTypeSelect').on('changed.bs.select', filterCtnSizes);

            filterCtnSizes();


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
                    const dateExist = $('.datepicker').val();
                    // // const tDateExist = $('.tdatepicker').val();
                    // console.log(dateExist);

                    if (iMonth !== null && iYear !== null && dateExist != '') {
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
