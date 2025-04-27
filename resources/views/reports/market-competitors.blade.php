@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Market Competitors',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Market Competitors</h2>
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
                        {{-- <a href="{{ route('reports.operator-wise-lifting.download', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
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
                            @if (request('from_date') && request('to_date'))
                            <p class="reportRange" style="display: none;">
                                {{ '(' . \Carbon\Carbon::parse(request('from_date'))->format('M y') . ' to ' . \Carbon\Carbon::parse(request('to_date'))->format('M y') . ')' }}
                            </p>
                            
                            @endif
                            <p class="reportTitle" style="display: none;" type="hidden">Market Competitors</p>
                            <thead>
                                <tr>
                                    <th colspan="12" class="text-center" style="font-size: 17px">Market Competitors</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">Operator </th>
                                    <th rowspan="2">Local Agent </th>
                                    <th rowspan="2">No. of Vessel </th>
                                    <th rowspan="2">No. of Call </th>
                                    <th rowspan="2">Eff. Capacity </th>
                                    <th rowspan="2">Eff .Cap/Week(4) </th>
                                    <th rowspan="2">Slot Partner </th>
                                    <th rowspan="2">Slot Buyer </th>
                                    <th colspan="3">Market Share</th>
                                    <th rowspan="2"> Sailing Freq </th>
                                </tr>
                                <tr>
                                    <th>Import %</th>
                                    <th>Export LDN%</th>
                                    <th>Export MTY%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $opt => $item)
                                    <tr>
                                        <td>{{ $opt }}</td>
                                        <td>{{ $item['local_agent'] }}</td>
                                        <td>{{ $item['numOfVsl'] }}</td>
                                        <td>{{ $item['numOfCall'] }}</td>
                                        <td>{{ $item['effectiveCapacity'] }}</td>
                                        <td>{{ $item['effCapPerWeek'] }}</td>
                                        <td>{{ $item['slotPartner'] }}</td>
                                        <td>{{ $item['slotBuyer'] }}</td>
                                        <td>{{ $item['import%'] }}</td>
                                        <td>{{ $item['exportLdn%'] }}</td>
                                        <td>{{ $item['exportMty%'] }}</td>
                                        <td>{{ $item['sailingFreq'] }}</td>
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
