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
                            <h2>Vessel Turn Around Time</h2>
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
                        <table class="tableFixHead table-bordered table2excel custom-table-report mb-3">
                            <thead>
                                <tr>
                                    <td rowspan="2">#sl</td>
                                    <td rowspan="2">Name of Vessel</td>
                                    <td rowspan="2">Bearthed at(jetty)</td>
                                    <td rowspan="2">GL/Gearless</td>
                                    <td rowspan="2">Eta (arrival_date arrival_time)</td>
                                    <td rowspan="2">OA Stay (arrival_date arrival_time to bearth_date bearth_time's total
                                        hours)</td>
                                    <td rowspan="2">Berth Date(date + time)</td>
                                    <td rowspan="2">Sailing Date(date+time)</td>
                                    <td rowspan="2">Bearth Stay(hrs of berth to sail datetime)</td>
                                    <td rowspan="2">Operator</td>
                                    <td colspan="3">Import</td>
                                    <td colspan="3">Export</td>
                                    <td rowspan="2">In-Out TTL Count</td>
                                    <td rowspan="2">In Out TTL Teus Count</td>
                                    <td rowspan="2">Turn Around Time (OA hrs + Bearth Stay hrs)</td>

                                </tr>
                                <tr>
                                    <td>LDN Teus</td>
                                    <td>MTY Teus</td>
                                    <td>TTL Teus</td>
                                    <td>LDN Teus</td>
                                    <td>MTY Teus</td>
                                    <td>TTL Teus</td>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($datas as $vessel)
                                    <tr>
                                        <td>{{ $vessel['sl'] }}</td>
                                        <td>{{ $vessel['name'] }}</td>
                                        <td>{{ $vessel['jetty'] }}</td>
                                        <td>{{ $vessel['gear'] }}</td>
                                        <td>{{ $vessel['eta'] }}</td>
                                        <td>{{ $vessel['oa_stay'] }}</td>
                                        <td>{{ $vessel['berth_datetime'] }}</td>
                                        <td>{{ $vessel['sail_datetime'] }}</td>
                                        <td>{{ $vessel['berth_stay'] }}</td>
                                        <td>{{ $vessel['operator'] }}</td>

                                        {{-- Import --}}
                                        <td>{{ $vessel['import_laden'] }}</td>
                                        <td>{{ $vessel['import_empty'] }}</td>
                                        <td><strong>{{ $vessel['import_total'] }}</strong></td>

                                        {{-- Export --}}
                                        <td>{{ $vessel['export_laden'] }}</td>
                                        <td>{{ $vessel['export_empty'] }}</td>
                                        <td><strong>{{ $vessel['export_total'] }}</strong></td>

                                        <td><strong>{{ $vessel['ttl_moves'] }}</strong></td>
                                        <td><strong>{{ $vessel['ttl_teus'] }}</strong></td>
                                        <td><strong>{{ $vessel['turn_around_time'] }}</strong></td>
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
