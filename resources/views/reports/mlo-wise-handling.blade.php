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
                            <h2>MLO Wise Container Handling</h2>
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
                        <a href="{{ route('reports.mlo.container-handling.download', ['from_date' => request()->get('from_date'), 'to_date' => request()->get('to_date'), 'route_id' => request()->get('route_id')]) }}"
                            class="btn btn-success btn-sm w-100">
                            <i class="fa fa-download" aria-hidden="true"></i> (xlsx)
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
                                    <th colspan="21" class="text-center" style="font-size: 16px">MLO Wise Container
                                        Handling</th>
                                </tr>
                                <tr>
                                    <th colspan="21" class="text-center">@include('components.route-range-summary')</th>
                                </tr>
                                <tr>
                                    <th rowspan="2">MLO</th>

                                    {{-- IMPORT --}}
                                    <th colspan="7" class="text-center">IMPORT</th>
                                    <th rowspan="2">LDN TEUs</th>
                                    <th rowspan="2">MTY TEUs</th>
                                    <th rowspan="2">TTL IMP. TEUs</th>

                                    {{-- EXPORT --}}
                                    <th colspan="7" class="text-center">EXPORT</th>
                                    <th rowspan="2">LDN TEUs</th>
                                    <th rowspan="2">MTY TEUs</th>
                                    <th rowspan="2">TTL EXP. TEUs</th>
                                </tr>
                                <tr>
                                    {{-- IMPORT details --}}
                                    <th>20'</th>
                                    <th>40'</th>
                                    <th>45'</th>
                                    <th>20R</th>
                                    <th>40R</th>
                                    <th>20M</th>
                                    <th>40M</th>

                                    {{-- EXPORT details --}}
                                    <th>20'</th>
                                    <th>40'</th>
                                    <th>45'</th>
                                    <th>20R</th>
                                    <th>40R</th>
                                    <th>20M</th>
                                    <th>40M</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $mlo => $item)
                                    <tr>
                                        <td>{{ $mlo }}</td>
                                        {{-- IMPORT --}}
                                        <td>{{ $item->where('type', 'import')->sum('dc20') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('dc40') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('dc45') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('r20') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('r40') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('mty20') }}</td>
                                        <td>{{ $item->where('type', 'import')->sum('mty40') }}</td>

                                        <td>
                                            {{ $item->where('type', 'import')->sum(fn($i) => $i->import_teu['laden'] ?? 0) }}
                                        </td>
                                        <td>
                                            {{ $item->where('type', 'import')->sum(fn($i) => $i->import_teu['empty'] ?? 0) }}
                                        </td>
                                        <td>
                                            {{ $item->where('type', 'import')->sum(fn($i) => $i->import_teu['total'] ?? 0) }}
                                        </td>

                                        {{-- EXPORT --}}
                                        <td>{{ $item->where('type', 'export')->sum('dc20') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('dc40') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('dc45') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('r20') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('r40') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('mty20') }}</td>
                                        <td>{{ $item->where('type', 'export')->sum('mty40') }}</td>

                                        <td>
                                            {{ $item->where('type', 'export')->sum(fn($i) => $i->export_teu['laden'] ?? 0) }}
                                        </td>
                                        <td>
                                            {{ $item->where('type', 'export')->sum(fn($i) => $i->export_teu['empty'] ?? 0) }}
                                        </td>
                                        <td>
                                            {{ $item->where('type', 'export')->sum(fn($i) => $i->export_teu['total'] ?? 0) }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>G.TTL</th>
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
            const totalColumns = 20;
            const startColIndex = 1;
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
