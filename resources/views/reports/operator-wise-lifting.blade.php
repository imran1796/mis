@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Operator Wise Container Lifting',
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
                            <h2>Operator Wise Container Lifting (Summary)</h2>
                        </div>

                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <form class="row px-3 mb-2" autocomplete="off">
                    <div class="col-md-2 px-1 form-group">
                        <select required id="pod" name="route_id[]" class="form-control form-control-sm selectpicker"
                            multiple>
                            @foreach ($pods as $pod)
                                <option {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }}
                                    value="{{ $pod->id }}">{{ $pod->name }}</option>
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
                        {{-- <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i class="fa fa-download"
                                aria-hidden="true"></i> xls</button> --}}
                        <a href="{{ route('reports.operator-wise-lifting.download', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download" aria-hidden="true"></i> (xlsx)
                        </a>
                        {{-- reports.operator-wise-lifting.download --}}
                        {{-- btn btn-info btn-fill btn-sm pull-right --}}
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
                        <table style="display: block" class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th colspan="2" class="font-weight-bold border-bottom text-center text-dark">Load
                                        Factor Import</th>
                                    <th colspan="2" class="font-weight-bold border-bottom text-center text-dark">Load
                                        Factor Export</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th class="font-weight-bold text-dark">LDN</th>
                                    <th class="font-weight-bold text-dark">MTY</th>
                                    <th class="font-weight-bold text-dark">LDN</th>
                                    <th class="font-weight-bold text-dark">MTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">Eff. Capacity</td>
                                    <td>{{ $data[1]['imp_ldn_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['imp_mty_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_ldn_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_mty_lf_eff'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Nom. Capacity</td>
                                    <td>{{ $data[1]['imp_ldn_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['imp_mty_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_ldn_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_mty_lf_nom'] ?? 0 }}</td>
                                </tr>
                            </tbody>


                        </table>
                    </div>
                    <div class="card-body">
                        <table class="tableFixHead table-bordered table-sm custom-table-report mb-3">
                            <thead>
                                <tr>
                                    <th colspan="17" style="font-size:16px" class="text-center">Operator Wise Container Lifting Summary</th>
                                </tr>
                                <tr>
                                    <th colspan="17" class="text-center">@include('components.route-range-summary')</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Operator</th>
                                    <th colspan="4" class="text-center">Import</th>
                                    <th colspan="4" class="text-center">Export</th>
                                    <th rowspan="2">T. VSL Call</th>
                                    <th rowspan="2">T. VSL Handled</th>
                                    <th rowspan="2">Eff Capacity</th>
                                    <th rowspan="2">NOM Capacity</th>
                                    <th rowspan="2">Import%</th>
                                    <th rowspan="2">EX Laden%</th>
                                    <th rowspan="2">EX Empty%</th>
                                </tr>
                                <tr>
                                    <th>LDN(T)</th>
                                    <th>MTY(T)</th>
                                    <th>LDN(%)</th>
                                    <th>MTY(%)</th>
                                    <th>LDN(T)</th>
                                    <th>MTY(T)</th>
                                    <th>LDN(%)</th>
                                    <th>MTY(%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($data[0]))
                                    @foreach ($data[0] as $opName => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $opName }}</td>
                                            <td>{{ $item['total_laden_import'] ?? '' }}</td>
                                            <td>{{ $item['total_empty_import'] ?? '' }}</td>
                                            <td>{{ $item['import_laden_eff'] ?? '' }}</td>
                                            <td>{{ $item['import_empty_eff'] ?? '' }}</td>
                                            <td>{{ $item['total_laden_export'] ?? '' }}</td>
                                            <td>{{ $item['total_empty_export'] ?? '' }}</td>
                                            <td>{{ $item['export_laden_eff'] ?? '' }}</td>
                                            <td>{{ $item['export_empty_eff'] ?? '' }}</td>
                                            <td>{{ $item['vessel_calls'] ?? '' }}</td>
                                            <td>{{ $item['unique_vessels'] ?? '' }}</td>
                                            <td>{{ $item['effective_capacity'] ?? '' }}</td>
                                            <td>{{ $item['nominal_capacity'] ?? '' }}</td>
                                            <td>{{ $item['import'] ?? '' }}</td>
                                            <td>{{ $item['export_laden'] ?? '' }}</td>
                                            <td>{{ $item['export_empty'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                            <tfoot>
                                @if (!empty($data[0]))
                                    <tr>
                                        <th class="text-center" colspan="2">Total</th>
                                        <th>{{ $data[0]->sum('total_laden_import') }}</th>
                                        <th>{{ $data[0]->sum('total_empty_import') }}</th>
                                        <th colspan="2"></th>
                                        <th>{{ $data[0]->sum('total_laden_export') }}</th>
                                        <th>{{ $data[0]->sum('total_empty_export') }}</th>
                                        <th colspan="2"></th>
                                        <th>{{ $data[0]->sum('vessel_calls') }}</th>
                                        <th>{{ $data[0]->sum('unique_vessels') }}</th>
                                        <th>{{ $data[0]->sum('effective_capacity') }}</th>
                                        <th>{{ $data[0]->sum('nominal_capacity') }}</th>
                                        <th>{{ round($data[0]->sum('import')).'%' }}</th>
                                        <th>{{ round($data[0]->sum('export_laden')).'%' }}</th>
                                        <th>{{ round($data[0]->sum('export_empty')).'%' }}</th>
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
    <script src="{{ asset('light-bootstrap/js/jquery.table2excel.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            initializeMonthYearPicker('.datepicker');
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

            $('#btnExcelJsExport').on('click', function() {
                // var heading_name = $("#heading_name").text();
                // $(".table2excel").table2excel({
                //     exclude: ".noExl",
                //     name: heading_name,
                //     filename: heading_name,
                //     fileext: ".xls",
                //     exclude_img: true,
                //     exclude_links: true,
                //     exclude_inputs: true,
                //     excel: {
                //         beforeSave: function() {
                //             // set the row height for all rows in the sheet
                //             var sheet = this.sheet;
                //             sheet.rowHeight(0, sheet.rowCount(),
                //                 30); // set row height to 30 pixels
                //         }
                //     }

                // });
            });

            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
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
