@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'SOC Out Bound',
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
                            <h2>SOC Outbound Marketing Strategy</h2>
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
                        @if (request('from_date') && request('to_date'))
                            <p class="reportRange" style="display: none;">
                                {{ '(' . \Carbon\Carbon::parse(request('from_date'))->format('M y') . ' to ' . \Carbon\Carbon::parse(request('to_date'))->format('M y') . ')' }}
                            </p>
                        @endif
                        <p class="reportTitle" style="display: none;" type="hidden">Outbound Market Strategy</p>
                        <table id="excelJsTable" class="tableFixHead table-bordered table2excel custom-table-report mb-3">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center" style="font-size: 17px">Outbound Market Strategy
                                    </th>
                                </tr>
                                <tr>
                                    <th rowspan="2">MLO Code</th>
                                    <th rowspan="2">MLO Name</th>
                                    <th rowspan="2">Local Agent Address</th>
                                    <th rowspan="2">Principal Contact Address</th>
                                    <th colspan="2">AVG Weekly Volume</th>
                                </tr>
                                <tr>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($datas as $opt => $item)
                                    <tr>
                                        <td>{{ $item['mlo_code'] }}</td>
                                        <td>{{ $item['mlo_name'] }}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $item['exportLdn'] }}</td>
                                        <td>{{ $item['exportMty'] }}</td>
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
