@extends('layouts.app', [
    'activePage' => 'mloWiseCount-index',
    'title' => 'GLA Admin',
    'navName' => 'MLO Wise Data',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Mlo Wise Data</h2>
                        </div>


                    </div>
                </div>

                <form class="row px-3 mb-2">
                    <div class="col-md-2 px-1 mt-1 form-group">
                        <select id="type" name="type" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="IMPORT">Import</option>
                            <option value="EXPORT">Export</option>
                        </select>
                    </div>

                    <div class="col-md-2 px-1 form-group">
                        <select id="mlo" name="mlo[]"
                            class="form-control form-control-sm selectpicker search-select" multiple>
                            <option value="">Select MLO</option>
                            @foreach ($mlos as $mlo)
                                <option value="{{ $mlo->mlo_code }}">{{ $mlo->mlo_code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" name="pod[]" class="form-control form-control-sm selectpicker" multiple>
                            <option value="">Select POD</option>
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
                    <!-- <div class="col-md-2 px-1 mt-1">
                                                                <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i class="fa fa-download"
                                                                        aria-hidden="true"></i> xls</button>
                                                            </div> -->
                    <div class="col-md-2 px-1"><button class="btn btn-success btn-sm w-100" id="mloSummaryReport"
                            type="button"> <i class="fa fa-download" aria-hidden="true"></i>Summary</button></div>

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
                        <table class="tableFixHead table-bordered table2excel custom-table-report mb-3 table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>MLO Code</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>DC20</th>
                                    <th>DC40</th>
                                    <th>DC45</th>
                                    <th>R20</th>
                                    <th>R40</th>
                                    <th>MTY20</th>
                                    <th>MTY40</th>
                                    <th>Laden Teus</th>
                                    <th>Empty Teus</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $mlo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @if (request('type') !== 'EXPORT' && request('type') !== 'IMPORT')
                                            @if ($index % 2 == 0)
                                                <td rowspan="2">{{ strtoupper($mlo->mlo_code) }}</td>
                                            @endif
                                        @else
                                            <td>{{ strtoupper($mlo->mlo_code) }}</td>
                                        @endif
                                        <td>{{ \Carbon\Carbon::parse($mlo->date)->format('F y') }}</td>
                                        <td>{{ ucfirst($mlo->type) }}</td>
                                        <td>{{ $mlo->dc20 ?? 0 }}</td>
                                        <td>{{ $mlo->dc40 ?? 0 }}</td>
                                        <td>{{ $mlo->dc45 ?? 0 }}</td>
                                        <td>{{ $mlo->r20 ?? 0 }}</td>
                                        <td>{{ $mlo->r40 ?? 0 }}</td>
                                        <td>{{ $mlo->mty20 ?? 0 }}</td>
                                        <td>{{ $mlo->mty40 ?? 0 }}</td>
                                        <td>{{ ($mlo->dc20 ?? 0) + ($mlo->r20 ?? 0) + (($mlo->dc40 ?? 0) + ($mlo->dc45 ?? 0) + ($mlo->r40 ?? 0)) * 2 }}
                                        </td>
                                        <td>{{ ($mlo->mty20 ?? 0) + ($mlo->mty40 ? $mlo->mty40 * 2 : 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <td colspan="4" class="text-center">Total</td>
                                <td>{{ $data->sum('dc20') }}</td>
                                <td>{{ $data->sum('dc40') }}</td>
                                <td>{{ $data->sum('dc45') }}</td>
                                <td>{{ $data->sum('r20') }}</td>
                                <td>{{ $data->sum('r45') }}</td>
                                <td>{{ $data->sum('mty20') }}</td>
                                <td>{{ $data->sum('mty40') }}</td>
                                <td>{{ $data->sum('dc20') + $data->sum('r40') + ($data->sum('dc45') + $data->sum('dc40') + $data->sum('r40')) * 2 }}
                                </td>
                                <td>{{ $data->sum('mty20') + $data->sum('mty40') * 2 }}</td>
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
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            initializeMonthYearPicker('.datepicker');
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
        $('.selectpicker').selectpicker({
            actionsBox: true,
            deselectAllText: 'Deselect All',
            selectAllText: 'Select All',
            countSelectedText: function(e, t) {
                return 1 == e ? "{0} item selected" : "{0} items selected"
            },
            selectedTextFormat: 'count'
        });
    </script>
@endpush
