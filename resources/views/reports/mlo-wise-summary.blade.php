@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'MLO',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Mlo Wise Summary</h2>
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
                        {{-- <a href="{{ route('reports.soc-inout-bound.download', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download" aria-hidden="true"></i> (xlsx)
                        </a> --}}
                        <button class="btn btn-success btn-sm w-100" id="btnExport" type="button"><i class="fa fa-download"
                            aria-hidden="true"></i> xls</button>
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
                            <p class="reportRange" style="display: none;" type="hidden">Date: {{ request('from_date') }} to {{ request('to_date') }}</p>
                            <p class="reportTitle" style="display: none;" type="hidden">Mlo_Wise_Summary</p>
                            <input class="reportRange" type="hidden" value="Date: {{ request('from_date') }} to {{ request('to_date') }}">
                            <input class="reportTitle" type="hidden" value="Mlo_Wise_Summary">
                            <thead>
                                <tr>
                                    <th rowspan="3">MLO</th>
                                    <th rowspan="3">Line Belongs To</th>
                                    <th rowspan="3">Mlo Details</th>
                                    @foreach (collect($results)->first()['permonth'] as $month => $d)
                                        <th colspan="4">{{ $month }}</th>
                                    @endforeach
                                    {{-- {{dd(collect($results))}} --}}

                                    {{-- <th colspan="4">foreach teus</th> --}}
                                    <th colspan="4">Total Teus</th>
                                    <th colspan="4">Average Teus</th>
                                </tr>
                                <tr>
                                    <th colspan="2">Import</th>
                                    <th colspan="2">Export</th>
                                    <th colspan="2">Import</th>
                                    <th colspan="2">Export</th>
                                    <th colspan="2">Import</th>
                                    <th colspan="2">Export</th>
                                </tr>
                                <tr>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                    <th>LDN</th>
                                    <th>MTY</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($results as $mlo => $dt)
                                    <tr>
                                        <td>{{ $mlo }}</td>
                                        <td>{{ $dt['lineBelongsTo'] }}</td>
                                        <td>{{ $dt['mloDetails'] }}</td>

                                        @foreach ($dt['permonth'] as $month => $t)
                                            <td>{{ $t['importLdnTeus'] }}</td>
                                            <td>{{ $t['importMtyTeus'] }}</td>
                                            <td>{{ $t['exportLdnTeus'] }}</td>
                                            <td>{{ $t['exportMtyTeus'] }}</td>
                                        @endforeach
                                        <td>{{ $dt['totalImportLdnTeus'] }}</td>
                                        <td>{{ $dt['totalImportMtyTeus'] }}</td>
                                        <td>{{ $dt['totalExportLdnTeus'] }}</td>
                                        <td>{{ $dt['totalExportMtyTeus'] }}</td>

                                        <td>{{ $dt['totalImportLdnTeus'] / count($dt['permonth']) }}</td>
                                        <td>{{ $dt['totalImportMtyTeus'] / count($dt['permonth']) }}</td>
                                        <td>{{ $dt['totalExportLdnTeus'] / count($dt['permonth']) }}</td>
                                        <td>{{ $dt['totalExportMtyTeus'] / count($dt['permonth']) }}</td>
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

            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush
