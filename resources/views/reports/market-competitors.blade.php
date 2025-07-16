@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Competitors Market Share',
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
                            <h2>Competitors Market Share</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form class="row px-3 mb-2" autocomplete="off">
                    {{-- @php
                        $specifiedOperator = [
                            'MCC',
                            'XPF',
                            'PIL',
                            'SOL',
                            'COSCO',
                            'OOCL',
                            'SKN',
                            'SNK',
                            'YML',
                            'SCC',
                            'HMM',
                            'ONE',
                            'HRL',
                            'APL',
                            'MSC',
                        ];
                    @endphp --}}
                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" name="route_id[]" required class="form-control form-control-sm selectpicker"
                            multiple>
                            @foreach ($pods as $pod)
                                <option {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }}
                                    value="{{ $pod->id }}">{{ $pod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input autocomplete="off" required placeholder="From Date"
                            class="form-control form-control-sm datepicker" type="text" name="from_date" id="from_date"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-md-2 mt-1 px-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input autocomplete="off" required placeholder="To Date"
                            class="form-control form-control-sm datepicker" type="text" name="to_date" id="to_date"
                            value="{{ request('to_date') }}">
                    </div>

                    {{-- <div class="col-md-2 px-1 form-group">
                        <select data-live-search="true" data-actions-box="true" id="operator" name="operators[]"
                            class="form-control form-control-sm search-select selectpicker" multiple>
                            @foreach ($specifiedOperator as $opt)
                                <option value="{{ $opt }}" selected>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div> --}}

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
                        <table id="excelJsTable"
                            class="tableFixHead table-bordered table2excel table-sm custom-table-report mb-3">
                            @if (request('from_date') && request('to_date'))
                                <p class="reportRange" style="display: none;">
                                    {{ '(' . \Carbon\Carbon::parse(request('from_date'))->format('M y') . ' to ' . \Carbon\Carbon::parse(request('to_date'))->format('M y') . ')' }}
                                </p>
                            @endif
                            <p class="reportTitle" style="display: none;" type="hidden">Competitors Market Share</p>
                            <thead>
                                <tr>
                                    <th colspan="12" class="text-center" style="font-size: 16px">Competitors Market Share</th>
                                </tr>
                                <tr>
                                    <th colspan="12" class="text-center">@include('components.route-range-summary')</th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2">Operator </th>
                                    <th class="text-center" rowspan="2">Local Agent </th>
                                    <th class="text-center" rowspan="2">No. of Vessel </th>
                                    <th class="text-center" rowspan="2">No. of Call </th>
                                    <th class="text-center" rowspan="2">Eff. Capacity </th>
                                    <th class="text-center" rowspan="2">Eff .Cap/Week(4) </th>
                                    <th class="text-center" rowspan="2">Slot Partner </th>
                                    <th class="text-center" rowspan="2">Slot Buyer </th>
                                    <th class="text-center" colspan="3">Market Share</th>
                                    <th class="text-center" rowspan="2"> Sailing Freq </th>
                                </tr>
                                <tr>
                                    <th class="text-center">Import %</th>
                                    <th class="text-center">Export LDN%</th>
                                    <th class="text-center">Export MTY%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $opt => $item)
                                    <tr>
                                        <td>{{ $opt }}</td>
                                        <td>{{ $item['local_agent'] }}</td>
                                        <td>{{ $item['numOfVsl'] }}</td>
                                        <td>{{ $item['numOfCall'] }}</td>
                                        <td>{{ round($item['effectiveCapacity']) }}</td>
                                        <td>{{ round($item['effCapPerWeek']) }}</td>
                                        <td>{{ $item['slotPartner'] }}</td>
                                        <td>{{ $item['slotBuyer'] }}</td>
                                        <td>{{ $item['import%'] }}</td>
                                        <td>{{ $item['exportLdn%'] }}</td>
                                        <td>{{ $item['exportMty%'] }}</td>
                                        <td>{{ $item['sailingFreq'] }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <th colspan="2" class="text-center">G.TTL</th>
                                <th>{{ collect($datas)->sum('numOfVsl') }}</th>
                                <th>{{ collect($datas)->sum('numOfCall') }}</th>
                                <th>{{ round(collect($datas)->sum('effectiveCapacity')) }}</th>
                                <th>{{ round(collect($datas)->sum('effCapPerWeek')) }}</th>
                                <th colspan="2"></th>
                                <th>{{ round(collect($datas)->sum('import%')) }}</th>
                                <th>{{ round(collect($datas)->sum('exportLdn%')) }}</th>
                                <th>{{ round(collect($datas)->sum('exportMty%')) }}</th>
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
                countSelectedText: (numSelected) =>
                    numSelected === 1 ? "{0} item selected" : "{0} items selected",
                selectedTextFormat: 'count > 3'
            });

            $('.selectpicker').on('loaded.bs.select', function() {
                $(this).parent().find('.bs-select-all').hide();
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
