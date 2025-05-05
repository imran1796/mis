@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Soc In/Out Bound',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>SOC In/Out Bound</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form class="row px-3 mb-2">
                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" name="route_id[]" class="form-control form-control-sm selectpicker" multiple>
                            @foreach ($pods as $pod)
                                <option value="{{ $pod->id }}">{{ $pod->name }}</option>
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
                        {{-- <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i class="fa fa-download"
                                aria-hidden="true"></i> xls</button> --}}
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
                        <table id="excelJsTable" class="tableFixHead table-bordered table2excel custom-table-report mb-3">
                            @if (request('from_date') && request('to_date'))
                                <p class="reportRange" style="display: none;">
                                    {{ '(' . \Carbon\Carbon::parse(request('from_date'))->format('M y') . ' to ' . \Carbon\Carbon::parse(request('to_date'))->format('M y') . ')' }}
                                </p>
                            @endif
                            <p class="reportTitle" style="display: none;" type="hidden">SOC_In/Out_Bound_Report</p>
                            <thead>

                                <tr>
                                    <th colspan="29" class="text-center" style="font-size: 17px">SOC In/Out Bound Report
                                    </th>
                                </tr>
                                <tr>
                                    <th rowspan="2">Month</th>
                                    <th colspan="10">Import</th>
                                    <th colspan="10">Export</th>
                                    <th colspan="3">Total Call</th>
                                    <th rowspan="2">Total</th>
                                    <th colspan="3">Total Vessel</th>
                                    <th rowspan="2">Total</th>
                                </tr>
                                <tr>
                                    @foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40', 'LDN Teus', 'MTY Teus', 'Total'] as $col)
                                        <th>{{ $col }}</th>
                                    @endforeach
                                    @foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40', 'LDN Teus', 'MTY Teus', 'Total'] as $col)
                                        <th>{{ $col }}</th>
                                    @endforeach
                                    <th>SIN</th>
                                    <th>CBO</th>
                                    <th>CGP</th>
                                    <th>SIN</th>
                                    <th>CBO</th>
                                    <th>CGP</th>
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
                                        <td>{{ $data['calls']['SIN'] }}</td>
                                        <td>{{ $data['calls']['CBO'] }}</td>
                                        <td>{{ $data['calls']['CGP'] }}</td>
                                        <td><strong>{{ $data['calls']['SIN'] + $data['calls']['CBO'] + $data['calls']['CGP'] }}</strong>
                                        </td>

                                        {{-- Vessels --}}
                                        <td>{{ $data['vessels_count']['SIN'] }}</td>
                                        <td>{{ $data['vessels_count']['CBO'] }}</td>
                                        <td>{{ $data['vessels_count']['CGP'] }}</td>
                                        <td><strong>{{ $data['vessels_count']['SIN'] + $data['vessels_count']['CBO'] + $data['vessels_count']['CGP'] }}</strong>
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

            $('#btnExcelJsExport').on('click', function() {
                var row1 = $('<tr class="text-center">').append(
                    '<td colspan="29">Sinokor Merchant Marine Co., Ltd.</td>');
                var row2 = $('<tr class="text-center">').append(
                    '<td colspan="29">Globe Link Associates Ltd.</td>');
                var row3 = $('<tr class="text-center">').append('<td colspan="29"></td>');
                $('thead').prepend(row1, row2, row3);

                var heading_name = 'SOC IN/OUT Bound';
                $(".table2excel").table2excel({
                    exclude: ".noExl",
                    name: heading_name,
                    filename: heading_name,
                    fileext: ".xls",
                    exclude_img: true,
                    exclude_links: true,
                    exclude_inputs: true,
                    excel: {
                        beforeSave: function() {
                            var sheet = this.sheet;
                            sheet.rowHeight(0, sheet.rowCount(),
                                30);
                        }
                    }

                });
                row1.remove();
                row2.remove();
                row3.remove();
            });

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
    </script>
@endpush
