@extends('layouts.app', [
    'activePage' => 'export-data',
    'title' => 'GLA Admin',
    'navName' => 'Export Data Analysis',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-datepicker-calendar {
            display: none;
        }

        .chart-container {
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            margin-bottom: 20px;
        }

        #chart1 {
            overflow-x: auto;
            overflow-y: hidden;
            height: 245px;
            min-width: 600px;
        }

        #chartPie {
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ct-label.ct-horizontal {
            font-size: 14px !important;
        }
    </style>

    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Export Data Analysis</h2>
                        </div>

                        <div class="pull-right">
                            {{-- @can('export-data.create') --}}
                            {{-- <a class="btn btn-success" href="{{ route('export-data.create') }}">Create Export Data</a> --}}
                            {{-- @endcan --}}
                        </div>
                    </div>
                </div>


                @if ($message = Session::get('success'))
                    <div class="alert alert-success">

                        <p>{{ $message }}</p>

                    </div>
                @endif

                <form class="row ">
                    <div class="col-sm-2 mt-1 pr-0 form-group">
                        <select name="report_type" id="report_type" class="form-control form-control-sm">
                            <option value="">Select Filter</option>
                            @foreach ([
            'mlo_wise' => 'MLO-wise',
            'commodity_wise' => 'Commodity-wise',
            'mlo_commodity_wise' => 'MLO-Commodity-wise',
            'pod_wise' => 'POD-wise',
            'mlo_pod_wise' => 'MLO-POD-wise',
            'pod_commodity_wise' => 'POD-Commodity-wise',
            'region_wise' => 'Region-wise',
            'region_pod_wise' => 'Region-POD-wise',
            'region_commodity_pod_wise' => 'Region-Commodity-POD-wise',
            'mlo_commodity_pod_wise' => 'MLO-Commodity-POD-wise',
            'mlo_pod_commodity_wise' => 'MLO-POD-Commodity-wise',
        ] as $key => $value)
                                <option value="{{ $key }}" {{ request('report_type') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-sm-2 pr-0 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input placeholder="From Date" class="form-control form-control-sm datepicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-sm-2 pr-0 mt-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input placeholder="To Date" class="form-control form-control-sm datepicker" type="text"
                            name="to_date" id="to_date" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-sm-2 pr-0 form-group">
                        <select data-live-search="true" class="form-control form-control-sm search-select selectpicker"
                            name="commodity[]" id="commodity" multiple title="Select Commodities">
                            @foreach ($commodities as $commodity)
                                <option value="{{ $commodity }}"
                                    {{ is_array(request('commodity')) && in_array($commodity, request('commodity')) ? 'selected' : '' }}>
                                    {{ $commodity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2 pr-0 form-group">
                        <select data-live-search="true"
                            class="form-control selectpicker form-control-sm search-select selectpicker" name="mlo[]"
                            id="mlo" multiple title="Select MLOs">
                            @foreach ($mlos as $mlo)
                                <option value="{{ $mlo }}"
                                    {{ is_array(request('mlo')) && in_array($mlo, request('mlo')) ? 'selected' : '' }}>
                                    {{ $mlo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2 pr-0 form-group">
                        <select data-live-search="true"
                            class="form-control selectpicker form-control-sm search-select selectpicker" name="pod[]"
                            id="pod" multiple title="Select PODs">
                            <option value="">Select PODs</option>
                            @foreach ($pods as $pod)
                                <option value="{{ $pod }}"
                                    {{ is_array(request('pod')) && in_array($pod, request('pod')) ? 'selected' : '' }}>
                                    {{ $pod }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2 pr-0 form-group">
                        <select data-live-search="true"
                            class="form-control selectpicker form-control-sm search-select selectpicker" name="region[]"
                            id="region" multiple title="Select Regions">
                            <option value="">Select Regions</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region }}"
                                    {{ is_array(request('region')) && in_array($region, request('region')) ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2 pr-0 mt-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
                    </div>
                    <div class="col-sm-2 pr-0 mt-1">
                        <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i
                                class="fa fa-download" aria-hidden="true"></i> xlsx</button>
                    </div>
                </form>
                <div class="row mb-2">
                    <div class="col-sm-2 pr-0 offset-sm-8">
                        <form class="" action="{{ route('export-data.report.port') }}" method="POST">
                            @csrf
                            @method('post')
                            <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                            <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                            {{-- @foreach ((array) request('pod') as $pod)
                                <input type="hidden" name="pod[]" value="{{ $pod }}">
                            @endforeach --}}

                            <button class="btn btn-success btn-sm w-100" id="exportVolByPort" type="submit"> <i
                                    class="fa fa-download" aria-hidden="true"></i> By
                                Port</button>
                        </form>
                    </div>
                    <div class="col-sm-2 ">
                        <form class="" action="{{ route('export-data.report.region') }}" method="POST">
                            @csrf
                            @method('post')
                            <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                            <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                            {{-- @foreach ((array) request('pod') as $pod)
                                <input type="hidden" name="pod[]" value="{{ $pod }}">
                            @endforeach --}}

                            <button class="btn btn-success btn-sm w-100" type="submit"> <i class="fa fa-download"
                                    aria-hidden="true"></i> By Region</button>
                        </form>
                    </div>
                </div>

                {{-- exportVolByPort --}}

                {{-- end filters --}}

                {{-- start container summary --}}
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <table class="table-bordered custom-table-report table-sm">
                                    <tr class="text-center">
                                        <th>Count/Container Size</th>
                                        <th>20</th>
                                        <th>40</th>
                                        <th>45</th>
                                        <th>20R</th>
                                        <th>40R</th>
                                        <th>Total</th>
                                    </tr>
                                    <tr class="text-center bg-light">
                                        <th>Unit</th>
                                        <td>{{ $exportDatas[1]['20ft']??0 }}</td>
                                        <td>{{ $exportDatas[1]['40ft']??0 }}</td>
                                        <td>{{ $exportDatas[1]['45ft']??0 }}</td>
                                        <td>{{ $exportDatas[1]['20R']??0 }}</td>
                                        <td>{{ $exportDatas[1]['40R']??0 }}</td>
                                        <td>{{ $exportDatas[1]['total_unit']??0 }}</td>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Teus</th>
                                        <td>{{ ($exportDatas[1]['20ft']??0) }}</td>
                                        <td>{{ ($exportDatas[1]['40ft']??0) * 2 }}</td>
                                        <td>{{ ($exportDatas[1]['45ft']??0) * 2 }}</td>
                                        <td>{{ ($exportDatas[1]['20R']??0) }}</td>
                                        <td>{{ ($exportDatas[1]['40R']??0) * 2 }}</td>
                                        <td>{{ ($exportDatas[1]['total_teus']??0) }}</td>
                                    </tr>

                                </table>
                            </div>
                            <div class="col-sm-4">
                                <table class="table-bordered custom-table-report table-sm">
                                    <tr class="text-center">
                                        <td>Total Commodity</td>
                                        <td>{{ $exportDatas[1]['total_commodity']??0 }}</td>
                                    </tr>
                                    <tr class="text-center bg-light">
                                        <td>Total POD</td>
                                        <td>{{ $exportDatas[1]['total_pod']??0 }}</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- end container summary --}}

                {{-- start chart --}}
                {{-- <div class="row mb-2" style="padding-right:15px; padding-left:15px" id="barChart">
                    <div class="card m-0 p-0 col-sm-9">
                        <div class="card-body mx-0 px-0 mb-0 chart-container">
                            <div id="chart1"></div>
                        </div>
                        <div class="card-footer mt-0 pt-0">
                            <div class="legend">
                                <i class="fa fa-circle text-info"></i> {{ __('CTN 20') }}
                                <i class="fa fa-circle text-danger"></i> {{ __('CTN 40') }}
                            </div>
                        </div>
                    </div>
                    <div class="card m-0 p-0 col-sm-3">
                        <div class="card-header">TEUs %</div>
                        <div class="card-body m-0 p-0">
                            <div id="chartPie"></div>
                        </div>
                    </div>
                </div> --}}
                {{-- end chart --}}

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
                            <thead>
                                @php
                                    $colspan = match (substr_count(request('report_type'), '_')) {
                                        3 => 12,
                                        2 => 11,
                                        1 => 10,
                                        default => 11,
                                    };
                                @endphp

                                <tr class="text-center merge-row">
                                    <th class="text-center" colspan="{{ $colspan }}">
                                        <p class="m-0 reportTitle" style="font-size: 18px">
                                            <strong>{{ request()->filled('report_type')
                                                ? Str::title(str_replace('_', ' ', request('report_type'))) . ' Report'
                                                : 'Export Data' }}
                                            </strong>
                                        </p>
                                    </th>
                                </tr>

                                @if (request()->filled('commodity'))
                                    <tr>
                                        <th class="text-center" colspan="{{ $colspan }}">
                                            Commodity: {{ count(request('commodity')) > 20 ? count(request('commodity')) . ' Commodities' : implode(', ', request('commodity')) }}
                                        </th>
                                    </tr>
                                @endif
                                @if (request()->filled('mlo'))
                                    <tr>
                                        <th class="text-center" colspan="{{ $colspan }}">
                                            MLO: {{ count(request('mlo')) > 20 ? count(request('mlo')) . ' MLOs' : implode(', ', request('mlo')) }}
                                        </th>
                                    </tr>
                                @endif
                                @if (request()->filled('pod'))
                                    <tr>
                                        <th class="text-center" colspan="{{ $colspan }}">
                                            POD: {{ count(request('pod')) > 20 ? count(request('pod')) . ' PODs' : implode(', ', request('pod')) }}
                                        </th>
                                    </tr>
                                @endif
                                @if (request()->filled('from_date') && request()->filled('to_date'))
                                    <tr>
                                        <th class="text-center reportRange" colspan="{{ $colspan }}">
                                            Date: {{ request('from_date') }} to {{ request('to_date') }}
                                        </th>
                                    </tr>
                                @endif

                                <tr>
                                    @unless (request()->filled('report_type'))
                                        <th>#</th>
                                        <th>MLO</th>
                                        <th>Commodity</th>
                                        <th>POD</th>
                                    @endunless

                                    @if (request()->filled('report_type'))
                                        <th>#</th>
                                        @foreach ($exportDatas[0][1] as $header)
                                            <th>{{ ucfirst($header) }}</th>
                                        @endforeach
                                    @endif

                                    <th>20ft</th>
                                    <th>40ft</th>
                                    <th>45ft</th>
                                    <th>20R</th>
                                    <th>40R</th>

                                    @if (request()->filled('report_type'))
                                        <th>Units</th>
                                        <th>TEUs</th>
                                        <th>TEUs %</th>
                                    @endif

                                    @unless (request()->filled('report_type'))
                                        <th>Trade</td>
                                        <th>Date</td>
                                        @endunless

                                </tr>
                            </thead>
                            <tbody>

                                @foreach (request()->filled('report_type') ? $exportDatas[0][0] : $exportDatas[0] as $row)
                                    <tr data-series="{{ json_encode([
                                        '20ft' => $row['20ft'] ?: 0,
                                        '40ft' => $row['40ft'] ?: 0,
                                        '45ft' => $row['45ft'] ?: 0,
                                        '20R' => $row['20R'] ?: 0,
                                        '40R' => $row['40R'] ?: 0,
                                        'Teus' => $row['teus'] ?: 0,
                                    ]) }}"
                                        data-labels="{{ request()->filled('report_type') ? json_encode(implode('/', array_map(fn($key) => $row[$key], $exportDatas[0][1]))) : '' }}">
                                        @unless (request()->filled('report_type'))
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $row->mlo }}</td>
                                            <td>{{ $row->commodity }}</td>
                                            {{-- <td>{{ $row->pod . ' (' . $row->port_code . ')' }}</td> --}}
                                            <td>{{ $row->pod }}</td>
                                        @endunless

                                        @if (request()->filled('report_type'))
                                            {{-- <td>
                                                <input type="checkbox" class="select-row" />
                                            </td> --}}
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            @foreach ($exportDatas[0][1] as $key)
                                                <td>
                                                    {{-- <input type="checkbox" class="select-row" />  --}}
                                                    {{ $row[$key] }}
                                                </td>
                                            @endforeach
                                        @endif

                                        <td>{{ $row['20ft'] ?: '-' }}</td>
                                        <td>{{ $row['40ft'] ?: '-' }}</td>
                                        <td>{{ $row['45ft'] ?: '-' }}</td>
                                        <td>{{ $row['20R'] ?: '-' }}</td>
                                        <td>{{ $row['40R'] ?: '-' }}</td>

                                        @if (request()->filled('report_type'))
                                            <td>{{ $row['unit_count'] }}</td>
                                            <td>{{ $row['teus'] }}</td>
                                            <td>{{ $row['teus_percentage'] }}%</td>
                                        @endif

                                        @unless (request()->filled('report_type'))
                                            <td>{{ $row->trade }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->date)->format('M Y') }}</td>
                                        @endunless
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if (request()->filled('report_type'))
                                    <tr>
                                        {{-- <td colspan="{{substr_count(request('report_type'), '_') === 2 ? (request('report_type') == 1 ? 5 : 4) : 4}}">Total</td> --}}
                                        <th class="text-center"
                                            colspan="{{ match (substr_count(request('report_type'), '_')) {
                                                3 => 3,
                                                2 => 3,
                                                default => 2,
                                            } }}">
                                            Total</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('20ft') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('40ft') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('45ft') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('20R') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('40R') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('unit_count') }}</th>
                                        <th>{{ collect($exportDatas[0][0])->sum('teus') }}</th>
                                        <th>{{ round(collect($exportDatas[0][0])->sum('teus_percentage')) }}</th>
                                    </tr>
                                @else
                                    <tr>
                                        <th class="text-center" colspan="4">Total</th>
                                        <th>{{ $exportDatas[0]->sum('20ft')??0 }}</th>
                                        <th>{{ $exportDatas[0]->sum('40ft')??0 }}</th>
                                        <th>{{ $exportDatas[0]->sum('45ft')??0 }}</th>
                                        <th>{{ $exportDatas[0]->sum('20R') ??0}}</th>
                                        <th>{{ $exportDatas[0]->sum('40R') ??0}}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                @endif
                            </tfoot>


                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- <script src="{{ asset('light-bootstrap/js/jquery.table2excel.min.js') }}"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script> --}}
    <script>
        let selectedData = [];
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //date picker in date input field
            initializeMonthYearPicker('.datepicker');
            // updateChart();

            //auto search
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // export to xls
            // $('#btnExcelJsExport').on('click', async function () {
            //     const workbook = new ExcelJS.Workbook();
            //     const worksheet = workbook.addWorksheet("Sheet1");

            //     const table = document.getElementById("myTable");

            //     // Loop through table rows
            //     for (let i = 0; i < table.rows.length; i++) {
            //         const row = table.rows[i];
            //         const cells = Array.from(row.cells).map(cell => cell.innerText);
            //         const excelRow = worksheet.addRow(cells);

            //         // Apply border to each cell in the row
            //         excelRow.eachCell(cell => {
            //             cell.border = {
            //                 top: { style: "thin" },
            //                 left: { style: "thin" },
            //                 bottom: { style: "thin" },
            //                 right: { style: "thin" }
            //             };
            //         });
            //     }
            //     // Example: merge A1 and B1
            //     worksheet.mergeCells("A1:I1");
            //   //  worksheet.getCell("A1").value = "Merged Header"; // Change to your actual header name
            //     worksheet.getCell("A1").alignment = {
            //         horizontal: "center",
            //         vertical: "middle",
            //        wrapText: true
            //     };
            //     worksheet.getCell("A1").font = { bold: true };

            //     // auto fit column


            //     worksheet.columns.forEach(column => {
            //         let maxLength = 0;
            //         column.eachCell({ includeEmpty: true }, cell => {
            //             const cellValue = cell.value ? cell.value.toString() : "";
            //             maxLength = Math.max(maxLength, cellValue.length);
            //         });
            //        // column.width = maxLength + 1;
            //         column.width = Math.min(maxLength + 2, 15);
            //     });

            //     // Export to Excel file
            //     const buffer = await workbook.xlsx.writeBuffer(); 
            //     const blob = new Blob([buffer], {
            //         type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            //     });
            //     const link = document.createElement("a");
            //     link.href = URL.createObjectURL(blob);
            //     link.download = "table-with-borders.xlsx";
            //     link.click();
            // });

            // $('#btnExcelJsExport').on('click', async function() {
            //     const workbook = new ExcelJS.Workbook();
            //     const worksheet = workbook.addWorksheet("Export Report");

            //     const table = document.getElementById("excelJsTable");
            //     let mergeInstructions = [];
            //     for (let i = 0; i < table.rows.length; i++) {
            //         const row = table.rows[i];
            //         const cells = Array.from(row.cells);
            //         const rowData = cells.map(cell => cell.innerText.trim());

            //         // Add row to worksheet
            //         const excelRow = worksheet.addRow(rowData);

            //         // Apply border and styling to each cell
            //         excelRow.eachCell((cell, colNumber) => {
            //             cell.border = {
            //                 top: {
            //                     style: "thin"
            //                 },
            //                 left: {
            //                     style: "thin"
            //                 },
            //                 bottom: {
            //                     style: "thin"
            //                 },
            //                 right: {
            //                     style: "thin"
            //                 }
            //             };
            //             cell.alignment = {
            //                 horizontal: "center",
            //                 vertical: "middle",
            //                 wrapText: true
            //             };
            //         });

            //         // Handle merged headers (colspan)
            //         const firstCell = cells[0]
            //         if (firstCell && firstCell.hasAttribute("colspan")) {
            //             const colspan = parseInt(firstCell.getAttribute("colspan"));
            //             const cellRange = `A${i + 1}:${String.fromCharCode(64 + colspan)}${i + 1}`;
            //             mergeInstructions.push(cellRange);

            //             // Optional: center the merged text and make it bold
            //             worksheet.mergeCells(cellRange);
            //             const mergedCell = worksheet.getCell(`A${i + 1}`);
            //             mergedCell.font = {
            //                 bold: true
            //             };
            //         }
            //     }

            //     // Auto fit column widths (with limit)
            //     worksheet.columns.forEach(column => {
            //         let maxLength = 0;
            //         column.eachCell({
            //             includeEmpty: true
            //         }, cell => {
            //             const value = cell.value ? cell.value.toString() : "";
            //             maxLength = Math.max(maxLength, value.length);
            //         });
            //         column.width = Math.min(maxLength + 2, 15);
            //     });


            //     // Generate and download Excel file
            //     const buffer = await workbook.xlsx.writeBuffer();
            //     const blob = new Blob([buffer], {
            //         type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            //     });

            //     const link = document.createElement("a");
            //     link.href = URL.createObjectURL(blob);
            //     link.download = "export_report.xlsx";
            //     document.body.appendChild(link);
            //     link.click();
            //     document.body.removeChild(link);
            // });

            function columnNumberToName(num) {
                let columnName = '';
                while (num > 0) {
                    let remainder = (num - 1) % 26;
                    columnName = String.fromCharCode(65 + remainder) + columnName;
                    num = Math.floor((num - 1) / 26);
                }
                return columnName;
            }

            // Process thead, tbody, and tfoot
            // function processHtmlTableToExcelJs(table, worksheet) {
            //     const cellMap = {}; // Track occupied cells (row,col)
            //     let currentRowIndex = 1;

            //     const sections = ['thead', 'tbody', 'tfoot'];

            //     sections.forEach(section => {
            //         const rows = table.querySelectorAll(`${section} tr`);

            //         rows.forEach((row) => {
            //             const cells = Array.from(row.children);
            //             let currentColIndex = 1;

            //             const rowData = []; // for normal cells

            //             cells.forEach((cell) => {
            //                 // Skip already occupied cells (because of rowspan/colspan)
            //                 while (cellMap[`${currentRowIndex},${currentColIndex}`]) {
            //                     currentColIndex++;
            //                 }

            //                 const colspan = parseInt(cell.getAttribute("colspan") || 1);
            //                 const rowspan = parseInt(cell.getAttribute("rowspan") || 1);

            //                 const startCol = currentColIndex;
            //                 const endCol = currentColIndex + colspan - 1;
            //                 const startRow = currentRowIndex;
            //                 const endRow = currentRowIndex + rowspan - 1;

            //                 // Merge cells if colspan or rowspan > 1
            //                 if (colspan > 1 || rowspan > 1) {
            //                     const startCell = columnNumberToName(startCol) + startRow;
            //                     const endCell = columnNumberToName(endCol) + endRow;
            //                     worksheet.mergeCells(`${startCell}:${endCell}`);
            //                 }

            //                 const excelCell = worksheet.getCell(columnNumberToName(
            //                     startCol) + startRow);
            //                 excelCell.value = cell.textContent.trim();
            //                 excelCell.alignment = {
            //                     vertical: 'middle',
            //                     horizontal: 'center',
            //                     wrapText: true
            //                 };
            //                 excelCell.border = {
            //                     top: {
            //                         style: 'thin'
            //                     },
            //                     left: {
            //                         style: 'thin'
            //                     },
            //                     bottom: {
            //                         style: 'thin'
            //                     },
            //                     right: {
            //                         style: 'thin'
            //                     }
            //                 };
            //                 if (section !== 'tbody') {
            //                     excelCell.font = {
            //                         bold: true
            //                     };
            //                 }

            //                 // Mark occupied cells
            //                 for (let r = startRow; r <= endRow; r++) {
            //                     for (let c = startCol; c <= endCol; c++) {
            //                         cellMap[`${r},${c}`] = true;
            //                     }
            //                 }

            //                 currentColIndex += colspan;
            //             });

            //             currentRowIndex++;
            //         });
            //     });
            // }

            // $('#btnExcelJsExport').on('click', async function() {
            //     try {
            //         const reportTitle = $('.reportTitle').text().trim().replace(/\s+/g, '_');
            //         const reportRange = $('.reportRange').text().trim().replace(/\s+/g, '_');

            //         const workbook = new ExcelJS.Workbook();
            //         const worksheet = workbook.addWorksheet("Export Report");

            //         const table = document.getElementById("excelJsTable");

            //         if (!table) {
            //             throw new Error("Table not found");
            //         }

            //         processHtmlTableToExcelJs(table, worksheet);

            //         // Auto-fit columns width (with max width limit)
            //         worksheet.columns.forEach((column) => {
            //             let maxLength = 0;
            //             column.eachCell({
            //                 includeEmpty: true
            //             }, (cell) => {
            //                 const value = cell.value ? cell.value.toString() : "";
            //                 maxLength = Math.max(maxLength, value.length);
            //             });
            //             column.width = Math.min(maxLength + 2, 15);
            //         });

            //         // Download Excel
            //         const buffer = await workbook.xlsx.writeBuffer();
            //         const blob = new Blob([buffer], {
            //             type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            //         });

            //         const link = document.createElement('a');
            //         link.href = URL.createObjectURL(blob);
            //         link.download = `${reportTitle}_${reportRange}.xlsx`;
            //         document.body.appendChild(link);
            //         link.click();
            //         document.body.removeChild(link);

            //     } catch (error) {
            //         console.error("Export failed:", error);
            //         alert("Failed to export Excel: " + error.message);
            //     }
            // });


            $('.select-row').on('change', function() {
                let labels = $(this).closest('tr').data('labels');
                let series = $(this).closest('tr').data('series');

                // console.log(labels);
                let rowData = {
                    labels: labels ? labels.split('/') : [],
                    series: Object.values(series).map(value => parseInt(value))
                };

                if ($(this).is(':checked')) {
                    selectedData.push(rowData);
                } else {
                    selectedData = selectedData.filter(item => JSON.stringify(item) !== JSON.stringify(
                        rowData));
                }

                // updateChart();
            });

        });

        // function updateChart() {
        //     if (selectedData.length === 0) {
        //         $('#barChart').hide();
        //         return;
        //     }

        //     $('#barChart').show();

        //     let allLabels = [];

        //     let _20 = [];
        //     let _40 = [];
        //     let _teus = [];
        //     let max_val = 0;

        //     selectedData.forEach(row => {
        //         // console.log(row.labels,row.labels.join('/'));
        //         let labelText = row.labels.join('/');
        //         allLabels.push(labelText);
        //         _20.push(row.series[0] + row.series[3]);
        //         _40.push(row.series[1] + row.series[2] + row.series[4]);
        //         _teus.push(row.series[5]);
        //         max_val = Math.max(max_val, row.series[0] || 0, row.series[1] || 0);
        //     });

        //     let allSeries = [_20, _40];

        //     demo.initExportDataBar(allLabels, allSeries, max_val);
        //     demo.initExportDataChart(_teus);
        // }

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

        $('.selectpicker').selectpicker({
            actionsBox: true,
            deselectAllText: 'Deselect All',
            countSelectedText: function(e, t) {
                return 1 == e ? "{0} item selected" : "{0} items selected"
            },
            selectedTextFormat: 'count'
        }).on('loaded.bs.select', function() {
            $(this).parent().find('.bs-select-all').hide();
        });;
    </script>
@endpush
