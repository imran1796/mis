@extends('layouts.app', [
    'activePage' => 'vesselInfo-index',
    'title' => 'GLA Admin',
    'navName' => 'Vessel Info',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-timepicker-container {
            z-index: 9999 !important;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Vessel Info Report</h2>
                        </div>


                    </div>
                </div>

                <form class="row">
                    {{-- Report Type --}}
                    <div class="col-sm-2 pr-0 form-group">
                        <select name="report_type" class="form-control form-control-sm selectpicker" title="Report Type">
                            @foreach (['operator-wise', 'operator-route-wise', 'route-wise'] as $type)
                                <option value="{{ $type }}" {{ request('report_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
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

                    {{-- Contianer Type --}}
                    <div class="col-sm-2 form-group">
                        <select name="ctn_type" class="form-control form-control-sm selectpicker" title="Mty/Ldn">
                            @foreach (['empty', 'laden'] as $type)
                                <option value="{{ $type }}" {{ request('ctn_type') == $type ? 'selected' : '' }}>
                                    {{ strtoupper($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Container Size --}}
                    <div class="col-sm-2  form-group">
                        <select name="ctn_size[]" class="form-control form-control-sm selectpicker" title="CTN Size"
                            multiple>
                            @foreach (['dc20', 'dc40', 'dc45', 'r20', 'r40', 'mty20', 'mty40'] as $type)
                                <option value="{{ $type }}"
                                    {{ collect(request('ctn_size'))->contains($type) ? 'selected' : '' }}>
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

                            $routes = [1 => 'Singapore', 2 => 'Colombo', 3 => 'Kolkata'];
                            $containerTypes = [
                                'dc20' => 'DC20',
                                'dc40' => 'DC40',
                                'dc45' => 'DC45',
                                'r20' => 'R40',
                                'r40' => 'R40',
                                'mty20' => 'MTY20',
                                'mty40' => 'MTY40',
                            ];
                            $shipmentTypes = ['export' => 'Export', 'import' => 'Import'];

                            $requestedRoutes = request()->filled('route_id')
                                ? (array) request('route_id')
                                : array_keys($routes);
                            $requestedShipmentTypes = request()->filled('shipment_type')
                                ? array_intersect(array_keys($shipmentTypes), (array) request('shipment_type'))
                                : array_keys($shipmentTypes);
                            $requestedContainerTypes = request()->filled('ctn_size')
                                ? array_intersect(array_keys($containerTypes), (array) request('ctn_size'))
                                : array_keys($containerTypes);

                        @endphp

                        <table id="excelJsTable"
                            class="tableFixHead table-bordered table2excel custom-table-report mb-3 table">
                            @php
                                $routeText =
                                    'Route: ' .
                                    collect($requestedRoutes)->map(fn($r) => $routes[$r] ?? $r)->implode(', ');
                            @endphp
                            <thead>
                                <tr>
                                    <th colspan="{{ 7 + count($requestedContainerTypes) }}" class="text-center">
                                        <h6 class="m-0 reportTitle text-center" style="font-size: 18px">
                                            <strong>
                                                {{ request()->filled('report_type')
                                                    ? Str::title(str_replace('_', ' ', request('report_type'))) . ' Report'
                                                    : 'Operator Wise Report' }}
                                            </strong>
                                        </h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="{{ 7 + count($requestedContainerTypes) }}" class="pt-0">
                                        <h6 class="text-center">{{ $routeText }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    {{-- <th>#</th> --}}
                                    <th>Operator</th>
                                    <th>Type</th>
                                    @foreach ($requestedContainerTypes as $type)
                                        <th>{{ $containerTypes[$type] }}</th>
                                    @endforeach
                                    <th>Unit</th>
                                    <th>TEU</th>
                                    <th>TEU%</th>
                                    <th>MTY+LDN%</th>
                                    <th>{{ request('ctn_type') . '%' ?? 'MTY/LDN%' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $operator => $rows)
                                    @php
                                        $rowspan = count($rows);
                                        $first = true;
                                        $i=0;
                                    @endphp
                                    @foreach ($rows as $type => $count)
                                        <tr>
                                            {{-- <td>{{$i+=1}}</td> --}}
                                            @if ($first)
                                                <td rowspan="{{ $rowspan }}">{{ $operator }}</td>
                                                @php $first = false; @endphp
                                            @endif

                                            <td>{{ ucfirst($type) }}</td>
                                            @foreach ($requestedContainerTypes as $type)
                                                <td>{{ $count[$type] }}</td>
                                            @endforeach

                                            <td>{{ $count['unit'] }}</td>
                                            <td>{{ $count['teus'] }}</td>
                                            <td>{{ $count['teus_p']??'' }}</td>
                                            <td>{{ $count['mty_ldn_p']??'' }}</td>
                                            <td>{{ $count['ctn_type_p']??'' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    @foreach ($requestedContainerTypes as $type)
                                        <th>
                                            {{ collect($data)->flatten(1)->sum(function ($item) use ($type) {
                                                return $item[$type] ?? 0;
                                            }) }}
                                        </th>
                                    @endforeach
                                    <th>
                                        {{ collect($data)->flatten(1)->sum('unit') }}
                                    </th>
                                    <th>
                                        {{ collect($data)->flatten(1)->sum('teus') }}
                                    </th>
                                    <th>
                                        {{ round(collect($data)->flatten(1)->sum('teus_p'), 2) }}
                                    </th>
                                    <th>
                                        {{ round(collect($data)->flatten(1)->sum('mty_ldn_p'), 2) }}
                                    </th>
                                    <th>
                                        {{ round(collect($data)->flatten(1)->sum('ctn_type_p'), 2) }}
                                    </th>
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

            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd',
                showButtonPanel: true,
                currentText: "Today",

                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        var buttonPane = $(inst.dpDiv).find('.ui-datepicker-buttonpane');

                        buttonPane.find('.ui-datepicker-current').off('click').on('click',
                            function() {
                                var today = new Date();
                                $(input).datepicker('setDate', today);
                                $.datepicker._hideDatepicker(input); //close after selecting
                                $(input).blur(); //prevent auto-focus/reopen
                            });
                    }, 1);
                }
            });

            $('.selectpicker').selectpicker({
                actionsBox: true,
                deselectAllText: 'Deselect All',
                selectAllText: 'Select All',
                countSelectedText: function(e, t) {
                    return 1 == e ? "{0} item selected" : "{0} items selected"
                },
                selectedTextFormat: 'count'
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
