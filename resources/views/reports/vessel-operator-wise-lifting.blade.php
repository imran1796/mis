@extends('layouts.app', [
    'activePage' => 'reports',
    'title' => 'GLA Admin',
    'navName' => 'Vessel Operator Wise Lifting',
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
                            <h2>Vessel Operator Wise Lifting</h2>
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
                        <a href="{{ route('reports.operator.container-handling', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download" aria-hidden="true"></i> (xlsx)
                        </a>
                    </div>
                    <div class="col-md-1 px-1 mt-1">
                        <a href="{{ route('reports.operator-wise-lifting', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100" target="_blank">
                            Summary
                        </a>
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
                        <table class="tableFixHead table-bordered table-sm custom-table-report">
                            <thead>
                                <tr>
                                    <th colspan="31" class="text-center" style="font-size:16px">OPT wise Container Lifting</th>
                                </tr>
                                <tr>
                                    <th colspan="31" class="text-center">@include('components.route-range-summary')</th>
                                </tr>

                                <tr>
                                    <th colspan="9" class="text-center">Arrival Departure Info</th>
                                    <th colspan="10" class="text-center">IMPORT</th>
                                    <th colspan="10" class="text-center">EXPORT</th>
                                    <th rowspan="2" class="text-center">TOTAL BOX</th>
                                    <th rowspan="2" class="text-center">TOTAL TEU</th>
                                </tr>
                                <tr>
                                    <th>SL. NO.</th>
                                    <th>VSL NAME</th>
                                    <th>A/D</th>
                                    <th>BERTH</th>
                                    <th>SLD</th>
                                    <th>OPT</th>
                                    <th>LOA</th>
                                    <th>G/GL</th>
                                    <th>Route</th>

                                    <th>20'</th>
                                    <th>40'</th>
                                    <th>45'</th>
                                    <th>20R</th>
                                    <th>40R</th>
                                    <th>20M</th>
                                    <th>40M</th>
                                    <th>LDN TEUs</th>
                                    <th>MTY TEUs</th>
                                    <th>TTL IMP TEUs</th>

                                    <th>E20'</th>
                                    <th>E40'</th>
                                    <th>E45'</th>
                                    <th>E20R</th>
                                    <th>E40R</th>
                                    <th>E20M</th>
                                    <th>E40M</th>
                                    <th>LDN TEUs</th>
                                    <th>MTY TEUs</th>
                                    <th>TTL EXP TEUs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $i => $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->vessel->vessel_name ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->arrival_date)->format('d.m.y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->berth_date)->format('d.m.y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->sail_date)->format('d.m.y') }}</td>
                                        <td>{{ $row->operator }}</td>
                                        <td>{{ $row->vessel->length_overall ?? '' }}</td>
                                        <td>{{ $row->vessel->crane_status ?? '' }}</td>
                                        <td>{{ $row->route->short_name ?? '' }}</td>

                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->dc20 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->dc40 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->dc45 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->r20 ?? 0 }}</td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->r40 ?? 0 }}</td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->mty20 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'import')->first()->mty40 ?? 0 }}
                                        </td>
                                        <td>{{ $row->import_teu['laden'] }}</td>
                                        <td>{{ $row->import_teu['empty'] }}</td>
                                        <td>{{ $row->import_teu['total'] }}</td>

                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->dc20 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->dc40 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->dc45 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->r20 ?? 0 }}</td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->r40 ?? 0 }}</td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->mty20 ?? 0 }}
                                        </td>
                                        <td>{{ $row->importExportCounts->where('type', 'export')->first()->mty40 ?? 0 }}
                                        </td>
                                        <td>{{ $row->export_teu['laden'] }}</td>
                                        <td>{{ $row->export_teu['empty'] }}</td>
                                        <td>{{ $row->export_teu['total'] }}</td>

                                        <td>{{ $row->import_box['total'] + $row->export_box['total'] }}</td>
                                        <td>{{ $row->import_teu['total'] + $row->export_teu['total'] }}</td>
                                    </tr>
                                    @if (isset($data[$i + 1]) && $data[$i + 1]->operator !== $data[$i]->operator)
                                        <td colspan="31"></td>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9" class="text-center">Grand Total</th>

                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                    <th></th>
                                    <th></th>
                                </tr>
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

            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            const table = $('.tableFixHead');
            const totalColumns = 22;
            const startColIndex = 9;
            let totals = Array(totalColumns).fill(0);

            table.find('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    if (index >= startColIndex && index < startColIndex + totalColumns) {
                        const val = parseFloat($(this).text()) || 0;
                        totals[index - startColIndex] += val;
                    }
                });
            });
            console.log(totals);

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
