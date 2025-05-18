@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Operator Wise Lifting',
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
                            <h2>Operator Wise Container Lifting</h2>
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

                    <div class="col-sm-2 pr-0 mt-1">
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
                        <table class="tableFixHead table-bordered custom-table-report mb-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th colspan="2" class="text-center">Load Factor Import</th>
                                    <th colspan="2" class="text-center">Load Factor Export</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Eff. Capacity</td>
                                    <td>{{ $data[1]['imp_ldn_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['imp_mty_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_ldn_lf_eff'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_mty_lf_eff'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Nom. Capacity</td>
                                    <td>{{ $data[1]['imp_ldn_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['imp_mty_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_ldn_lf_nom'] ?? 0 }}</td>
                                    <td>{{ $data[1]['exp_mty_lf_nom'] ?? 0 }}</td>
                                </tr>
                            </tbody>


                        </table>
                    </div>
                    <div class="card-body">
                        <table class="tableFixHead table-bordered custom-table-report mb-3">
                            <thead>
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
