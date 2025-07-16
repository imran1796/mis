@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'SOC Inbound and Outbound Volume Data',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-datepicker-calendar {
            display: none;
        }
        .ui-datepicker {
            z-index: 9999 !important;
        }

        thead tr:nth-child(1) th {
            top: 0;
            z-index: 4;
        }

        thead tr:nth-child(2) th {
            top: 30px;
            z-index: 3;
        }

        thead tr:nth-child(3) th {
            top: 60px;
            z-index: 2;
        }
        thead tr:nth-child(4) th {
            top: 90px;
            z-index: 1;
        }
    </style>

    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>SOC Inbound and Outbound Volume Data</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form class="row px-3 mb-2" autocomplete="off">
                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" required name="route_id[]" class="form-control form-control-sm selectpicker" multiple>
                            @foreach ($pods as $pod)
                                <option {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }} value="{{ $pod->id }}">{{ $pod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input autocomplete="off" required placeholder="From Date" class="form-control form-control-sm datepicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-md-2 mt-1 px-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input autocomplete="off" required placeholder="To Date" class="form-control form-control-sm datepicker" type="text"
                            name="to_date" id="to_date" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-1 px-1 mt-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
                    </div>

                    <div class="col-md-1 px-1 mt-1">
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
                            $teuCols = [
                                '20',
                                '40',
                                '45',
                                '20R',
                                '40R',
                                'MTY20',
                                'MTY40',
                                'LDN Teus',
                                'MTY Teus',
                                'Total',
                            ];
                            $allRoutes = [1 => 'SIN', 2 => 'CBO', 3 => 'CCU'];
                            $routes = array_intersect_key($allRoutes, array_flip((array) request('route_id', [])));
                        @endphp
                        <table id="excelJsTable" class="tableFixHead table-sm table-bordered table2excel custom-table-report mb-3">
                            @if (request('from_date') && request('to_date'))
                                <p class="reportRange" style="display: none;">
                                    {{ '(' . \Carbon\Carbon::parse(request('from_date'))->format('M y') . ' to ' . \Carbon\Carbon::parse(request('to_date'))->format('M y') . ')' }}
                                </p>
                            @endif
                            <p class="reportTitle" style="display: none;" type="hidden">SOC_In/Out_Bound_Report</p>
                            <thead>

                                <tr>
                                    <th colspan="{{ 23 + count($routes)*2 }}" class="text-center" style="font-size: 16px">SOC Inbound and Outbound Volume Data</th>
                                </tr>
                                <tr>
                                    <th colspan="{{ 23 + count($routes)*2 }}" class="text-center">@include('components.route-range-summary')</th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2">Month</th>
                                    <th class="text-center" colspan="{{ count($teuCols) }}">Import</th>
                                    <th class="text-center" colspan="{{ count($teuCols) }}">Export</th>
                                    <th class="text-center" colspan="{{ count($routes) }}">Total Call</th>
                                    <th class="text-center" rowspan="2">Total</th>
                                    <th class="text-center" colspan="{{ count($routes) }}">Total Vessel</th>
                                    <th class="text-center" rowspan="2">Total</th>
                                </tr>
                                <tr>
                                    {{-- Import TEUs --}}
                                    @foreach($teuCols as $col)
                                        <th class="text-center">{{ $col }}</th>
                                    @endforeach
                                
                                    {{-- Export TEUs --}}
                                    @foreach($teuCols as $col)
                                        <th class="text-center">{{ $col }}</th>
                                    @endforeach
                                
                                    {{-- Total Call by Route --}}
                                    @foreach($routes as $name)
                                        <th class="text-center">{{ $name }}</th>
                                    @endforeach
                                
                                    {{-- Total Vessel by Route --}}
                                    @foreach($routes as $name)
                                        <th class="text-center">{{ $name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $month => $data)
                                    @php
                                        $importTotal = 0;
                                        $importEmptyTeus = 0;
                                        $importLadenTeus = 0;
                                        $exportTotal = 0;
                                        $exportEmptyTeus = 0;
                                        $exportLadenTeus = 0;
                                    @endphp

                                    <tr>
                                        <td>{{ $month }}</td>

                                        {{-- Import columns --}}
                                        @foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40'] as $col)
                                            @php
                                                $val = $data['import'][$col];
                                                if (in_array($col, ['MTY20', 'MTY40'])) {
                                                    $importEmptyTeus += $col === 'MTY40' ? $val * 2 : $val;
                                                } else {
                                                    $importLadenTeus += in_array($col, ['40', '45', '40R'])
                                                        ? $val * 2
                                                        : $val;
                                                }
                                            @endphp
                                            <td>{{ $val }}</td>
                                        @endforeach

                                        @php
                                            $importTotal = $importLadenTeus + $importEmptyTeus;
                                        @endphp

                                        <td><strong>{{ $importLadenTeus }}</strong></td>
                                        <td><strong>{{ $importEmptyTeus }}</strong></td>
                                        <td><strong>{{ $importTotal }}</strong></td>

                                        {{-- Export columns --}}
                                        @foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40'] as $col)
                                            @php
                                                $val = $data['export'][$col];
                                                // Calculate Laden and Empty TEUs for Export
                                                if (in_array($col, ['MTY20', 'MTY40'])) {
                                                    $exportEmptyTeus += $col === 'MTY40' ? $val * 2 : $val;
                                                } else {
                                                    $exportLadenTeus += in_array($col, ['40', '45', '40R'])
                                                        ? $val * 2
                                                        : $val;
                                                }
                                            @endphp
                                            <td>{{ $val }}</td>
                                        @endforeach

                                        @php
                                            $exportTotal = $exportLadenTeus + $exportEmptyTeus;
                                        @endphp

                                        <td><strong>{{ $exportLadenTeus }}</strong></td>
                                        <td><strong>{{ $exportEmptyTeus }}</strong></td>
                                        <td><strong>{{ $exportTotal }}</strong></td>

                                        {{-- Calls --}}
                                        @foreach (array_intersect_key([1 => 'SIN', 2 => 'CBO', 3 => 'CCU'], array_flip((array) request()->route_id)) as $route)
                                            <td>{{ $data['calls'][$route] ?? 0 }}</td>
                                        @endforeach

                                        <td><strong>{{ $data['calls']['SIN'] + $data['calls']['CBO'] + $data['calls']['CCU'] }}</strong>
                                        </td>

                                        {{-- Vessels --}}
                                        @foreach (array_intersect_key([1 => 'SIN', 2 => 'CBO', 3 => 'CCU'], array_flip((array) request()->route_id)) as $route)
                                            <td>{{ $data['vessels_count'][$route] ?? 0 }}</td>
                                        @endforeach
                                        <td><strong>{{ $data['vessels_count']['SIN'] + $data['vessels_count']['CBO'] + $data['vessels_count']['CCU'] }}</strong>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>

                            <tfoot>
                            </tfoot>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('light-bootstrap/js/jquery.table2excel.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // $(".datepicker").datepicker({
            //     dateFormat: 'yy-mm-dd',
            //     showButtonPanel: true,
            //     currentText: "Today",

            //     beforeShow: function(input, inst) {
            //         setTimeout(function() {
            //             var buttonPane = $(inst.dpDiv).find('.ui-datepicker-buttonpane');

            //             buttonPane.find('.ui-datepicker-current').off('click').on('click',
            //                 function() {
            //                     var today = new Date();
            //                     $(input).datepicker('setDate', today);
            //                     $.datepicker._hideDatepicker(input); //close after selecting
            //                     $(input).blur(); //prevent auto-focus/reopen
            //                 });
            //         }, 1);
            //     }
            // });
            initializeMonthYearPicker('.datepicker');

            // $('#btnExcelJsExport').on('click', function() {
            //     var row1 = $('<tr class="text-center">').append(
            //         '<td colspan="29">Sinokor Merchant Marine Co., Ltd.</td>');
            //     var row2 = $('<tr class="text-center">').append(
            //         '<td colspan="29">Globe Link Associates Ltd.</td>');
            //     var row3 = $('<tr class="text-center">').append('<td colspan="29"></td>');
            //     $('thead').prepend(row1, row2, row3);

            //     var heading_name = 'SOC IN/OUT Bound';
            //     $(".table2excel").table2excel({
            //         exclude: ".noExl",
            //         name: heading_name,
            //         filename: heading_name,
            //         fileext: ".xls",
            //         exclude_img: true,
            //         exclude_links: true,
            //         exclude_inputs: true,
            //         excel: {
            //             beforeSave: function() {
            //                 var sheet = this.sheet;
            //                 sheet.rowHeight(0, sheet.rowCount(),
            //                     30);
            //             }
            //         }

            //     });
            //     row1.remove();
            //     row2.remove();
            //     row3.remove();
            // });

            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            calculateTotal();
        });

        function calculateTotal() {
            const $table = $('.table2excel');
            const $tbody = $table.find('tbody');
            const $tfoot = $table.find('tfoot');

            const numMonths = $tbody.find('tr').length;
            const colCount = $tbody.find('tr:first td').length;

            let totals = Array(colCount).fill(0);

            $tbody.find('tr').each(function() {
                $(this).find('td').each(function(index) {
                    const val = parseFloat($(this).text().replace(/,/g, '')) || 0;
                    totals[index] += val;
                });
            });

            const $totalRow = $('<tr>').append('<th>Total</th>');
            totals.slice(1).forEach(total => {
                $totalRow.append(`<th>${Math.round(total)}</th>`);
            });

            const $averageRow = $('<tr>').append('<th>AVG/Month</th>');
            totals.slice(1).forEach(total => {
                const avg = total / numMonths;
                $averageRow.append(`<th>${Math.round(avg)}</th>`);
            });

            $tfoot.html('').append($totalRow).append($averageRow);
        }

        function initializeMonthYearPicker(selector) {
            let isDateManuallySelected = false;

            $(selector).datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'M-yy',
                beforeShow: function() {
                    isDateManuallySelected = false;

                    const selDate = $(this).val();
                    if (selDate.length > 0) {
                        const iYear = selDate.slice(-4);
                        const iMonth = $.inArray(
                            selDate.slice(0, -5),
                            $(this).datepicker('option', 'monthNames')
                        );

                        if (iMonth !== -1) {
                            $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        }
                    }
                },
                onChangeMonthYear: function(year, month, inst) {
                    isDateManuallySelected = true;
                    $(this).datepicker('setDate', new Date(year, month - 1, 1));
                },
                onClose: function(dateText, inst) {
                    if (isDateManuallySelected) {
                        const iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        const iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();

                        if (iMonth !== null && iYear !== null) {
                            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        }
                    }
                }
            });
        }
    </script>
@endpush
