@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'MLO-NVOCC Wise Import and Export Volume',
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
            z-index: 5;
        }

        thead tr:nth-child(2) th {
            top: 30px;
            z-index: 4;
        }

        thead tr:nth-child(3) th {
            top: 60px;
            z-index: 3;
        }

        thead tr:nth-child(4) th {
            top: 90px;
            z-index: 2;
        }
        thead tr:nth-child(5) th {
            top: 120px;
            z-index: 1;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>MLO-NVOCC Wise Import and Export Volume</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form autocomplete="off" class="row px-3 mb-2">
                    {{-- @php
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
                    @endphp --}}
                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" autocomplete="off" required name="route_id[]"
                            class="form-control form-control-sm selectpicker" multiple title="Select Route">
                            @foreach ($pods as $pod)
                                <option value="{{ $pod->id }}"
                                    {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }}>
                                    {{ $pod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 form-group">
                        <select data-live-search="true" autocomplete="off" data-actions-box="true" id="mlo"
                            name="mlos[]" class="form-control form-control-sm search-select selectpicker" multiple>
                            @foreach ($mlos as $mlo)
                                {{-- <option value="{{ $mlo->mlo_code }}" {{($mlo->is_show_nvocc==1)||is_array($mlo->mlo_code,request('mlos'))?'selected':''}}>{{ $mlo->mlo_code }}</option> --}}
                                {{-- <option value="{{ $mlo->mlo_code }}" {{ in_array($mlo->mlo_code, $selectedMlos ?? []) ? 'selected' : '' }}> --}}
                                <option value="{{ $mlo->mlo_code }}"
                                    {{ (in_array($mlo->mlo_code, (array) request('mlos')) || $mlo->is_show_nvocc == 1) ? 'selected' : '' }}>
                                    {{ $mlo->mlo_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input required autocomplete="off" placeholder="From Date"
                            class="form-control form-control-sm datepicker" type="text" name="from_date" id="from_date"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-md-2 mt-1 px-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input required autocomplete="off" placeholder="To Date"
                            class="form-control form-control-sm datepicker" type="text" name="to_date" id="to_date"
                            value="{{ request('to_date') }}">
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
                            $selectedRouteIds = request('route_id', [1, 2, 3]);
                            $selectedRoutes = collect($pods)->whereIn('id', $selectedRouteIds);
                            $shortNames = $selectedRoutes->pluck('short_name')->implode(', ');
                            $perMonthCount = collect($results)
                            ->map(fn ($item) => count($item['permonth'] ?? []))
                            ->max() ?? 0;
                            $colspan = ($perMonthCount * 4) + 12;

                            $allMonths = collect($results)
                                ->flatMap(fn($dt) => array_keys($dt['permonth']))
                                ->unique()
                                ->sortBy(fn($m) => \Carbon\Carbon::parse($m)->month)
                                ->values();
                        @endphp
                        <table id="excelJsTable" class="tableFixHead table-bordered table-sm custom-table-report mb-3">
                            <p class="reportRange" style="display: none;" type="hidden">Date: {{ request('from_date') }} to
                                {{ request('to_date') }}</p>
                            <p class="reportTitle" style="display: none;" type="hidden">Mlo_Wise_Summary</p>
                            <input class="reportRange" type="hidden"
                                value="Date: {{ request('from_date') }} to {{ request('to_date') }}">
                            <input class="reportTitle" type="hidden" value="Mlo_Wise_Summary">
                            <thead>
                                <tr>
                                    <th style="font-size:16px" colspan="{{ $colspan }}" class="text-center">MLO-NVOCC Wise Import and Export Volume</th>
                                </tr>
                                <tr>
                                    <th colspan="{{ $colspan }}" class="text-center">@include('components.route-range-summary')</th>
                                </tr>

                                <tr>
                                    <th class="text-center" rowspan="3">#SL</th>
                                    <th class="text-center" rowspan="3">MLO Code</th>
                                    <th class="text-center" rowspan="3">Line Belongs To</th>
                                    <th class="text-center" rowspan="3">MLO Details</th>
                                    @foreach ($allMonths as $month)
                                        <th class="text-center" colspan="4" class="text-center">{{ $month }}</th>
                                    @endforeach

                                    <th class="text-center" colspan="4">Total Teus</th>
                                    <th class="text-center" colspan="4">Average Teus</th>
                                </tr>
                                <tr>
                                    @foreach ($allMonths as $month)
                                        <th class="text-center" colspan="2">Import</th>
                                        <th class="text-center" colspan="2">Export</th>
                                    @endforeach

                                    <th class="text-center" colspan="2">Import</th>
                                    <th class="text-center" colspan="2">Export</th>
                                    <th class="text-center" colspan="2">Import</th>
                                    <th class="text-center" colspan="2">Export</th>
                                </tr>
                                <tr>
                                    @foreach ($allMonths as $month)
                                        <th class="text-center">LDN</th>
                                        <th class="text-center">MTY</th>
                                        <th class="text-center">LDN</th>
                                        <th class="text-center">MTY</th>
                                    @endforeach

                                    <th class="text-center">LDN</th>
                                    <th class="text-center">MTY</th>
                                    <th class="text-center">LDN</th>
                                    <th class="text-center">MTY</th>
                                    <th class="text-center">LDN</th>
                                    <th class="text-center">MTY</th>
                                    <th class="text-center">LDN</th>
                                    <th class="text-center">MTY</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($results as $mlo => $dt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $mlo }}</td>
                                        <td>{{ $dt['lineBelongsTo'] }}</td>
                                        <td>{{ $dt['mloDetails'] }}</td>

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
                            <tfoot class="text-center">
                                <th colspan="4">Grand Total</th>
                                @foreach ($allMonths as $month)
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                @endforeach
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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
                selectAllText: 'Select All',
                countSelectedText: (numSelected) =>
                    numSelected === 1 ? "{0} item selected" : "{0} items selected",
                selectedTextFormat: 'count > 3'
            });

            $('.selectpicker').on('loaded.bs.select', function() {
                $(this).parent().find('.bs-select-all').hide();
            });

            const table = $('.tableFixHead');
            const startColIndex = 4;
            let totalMonth = @json($allMonths);
            const totalColumns = 12 + (totalMonth.length * 4);
            let totals = Array(totalColumns).fill(0);

            table.find('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    if (index >= startColIndex && index < startColIndex + totalColumns) {
                        const val = parseFloat($(this).text()) || 0;
                        totals[index - startColIndex] += val;
                    }
                });
            });
            // console.log(totals);

            const tfootRow = table.find('tfoot tr');
            tfootRow.find('th').each(function(index) {
                if (index >= 1 && index < 1 + totalColumns) {
                    $(this).text(totals[index - 1]);
                }
            });
        });

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
