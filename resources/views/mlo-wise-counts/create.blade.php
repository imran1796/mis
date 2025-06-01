@extends('layouts.app', [
    'activePage' => 'mloWiseCount-create',
    'title' => 'GLA Admin',
    'navName' => 'MLO',
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
                            <h2>MLO Wise Volume</h2>
                        </div>
                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form px-3">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="route_id" class="form-label">Route</label>
                                    </div>
                                    <div class="col-md-10">
                                        <select name="route_id" id="route_id" class="form-control" required>
                                            @foreach ($routes as $route)
                                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2"><label for="input-name">
                                            </i>{{ __('Date') }}
                                        </label></div>
                                    <div class="col-md-10"><input type="text" name="date"
                                            class="form-control form-control-sm datepicker"
                                            placeholder="Select Month and Year" aria-label="Select Month and Year" required>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2"><label for="input-name">
                                            </i>{{ __('XLS File') }}
                                        </label></div>
                                    <div class="col-md-10"><input required type="file" name="file"
                                            class="form-control form-control-sm" id="customFile"></div>
                                </div>

                                <div class="row pull-right">
                                    <div class="col-md-12"><button type="submit" class="btn btn-primary">Upload</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Records</h4>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-sm-4 mt-1 form-group">
                                        <input type="text" id="recordFromDate" name="from_date"
                                            class="form-control form-control-sm datepicker" placeholder="From Month"
                                            value="{{ request('from_date') }}">
                                    </div>

                                    <div class="col-sm-4 mt-1 form-group">
                                        <input type="text" id="recordToDate" name="to_date"
                                            class="form-control form-control-sm datepicker" placeholder="To Month"
                                            value="{{ request('to_date') }}">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <select name="route_id[]" id="recordRoutes"
                                            class="form-control form-control-sm selectpicker" title="Route" multiple>
                                            @foreach ($routes as $route)
                                                <option value="{{ $route->id }}"
                                                    {{ collect(request('route_id'))->contains($route->id) ? 'selected' : '' }}>
                                                    {{ $route->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-1 form-group">
                                        <button type="button" id="searchVesselDataBtn"
                                            class="btn btn-sm btn-primary w-100">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="tableFixHead table-bordered custom-table-report table-sm">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Route</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="mlo-table-body"></tbody>
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

            $('#searchVesselDataBtn').on('click', function() {
                fetchVesselData();
            });



            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('mloWiseCount.store') }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        demo.customShowNotification('success', response.success);

                        const fromDate = formData.get('date');
                        const toDate = formData.get('date');
                        const routeId = formData.get('route_id');

                        console.log(fromDate, toDate, routeId);

                        $('#uploadForm')[0].reset();
                        $('#uploadForm .selectpicker').selectpicker('refresh');

                        $('#recordFromDate').val(fromDate);
                        $('#recordToDate').val(toDate);
                        $('#recordRoutes').selectpicker('val', routeId);

                        fetchVesselData();
                    },
                    error: function(response) {
                        if (response.responseJSON.error) {
                            demo.customShowNotification('danger', response.responseJSON.error);
                        }
                        for (let field in response.responseJSON.errors) {
                            for (let i = 0; i < response.responseJSON.errors[field]
                                .length; i++) {
                                demo.customShowNotification('danger', response.responseJSON
                                    .errors[field][i]);
                            }
                        }
                    }
                });

            });

            $(document).on('click', '.confirm-delete', function() {
                const date = $(this).data('date');
                const route_id = $(this).data('route_id');
                const modalId = $(this).data('modal');
                const uid = $(this).data('uid');

                console.log(date, route_id, modalId, uid);

                $.ajax({
                    url: "{{ route('mloWiseCount.deleteByDateRoute') }}",
                    type: 'DELETE',
                    data: {
                        date: date,
                        route_id: route_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        demo.customShowNotification('success', response.success ||
                            'Deleted successfully.');
                        $(modalId).modal('hide');
                        setTimeout(function() {
                            $(`tr[data-uid="${uid}"]`).remove();
                        }, 2000);
                        
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.error) {
                            demo.customShowNotification('danger', res.error);
                        }
                        const errors = res?.errors || {};
                        Object.values(errors).forEach(messages =>
                            messages.forEach(msg => demo.customShowNotification('danger',
                                msg))
                        );
                    }
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

        function fetchVesselData() {
            const fromDate = $('#recordFromDate').val();
            const toDate = $('#recordToDate').val();
            const routeIds = $('#recordRoutes').val();

            $.ajax({
                url: '{{ route('mloWiseCount.perMonth') }}',
                method: 'GET',
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    route_id: routeIds
                },
                success: function(response) {
                    let tbody = '';
                    let i = 1;

                    Object.entries(response).forEach(([date, routes]) => {
                        Object.entries(routes).forEach(([routeId, items]) => {
                            items.forEach(item => {
                                const uid = `${date}${item.route_id}`;
                                const formattedDate = new Date(item.date)
                                    .toLocaleString('en-US', {
                                        month: 'long',
                                        year: 'numeric'
                                    });

                                tbody += `
                            <tr class="text-center" data-uid="${uid}">
                                <td>${i++}</td>
                                <td>${formattedDate}</td>
                                <td>${item.route?.name || 'N/A'}</td>
                                <td>
                                    @can('mloData-delete')
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmModal-${uid}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan

                                    <div class="modal fade" id="confirmModal-${uid}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning p-3">
                                                    <h5 class="modal-title">Confirmation Required</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <h5>Are you sure you want to delete records for ${formattedDate}?</h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-success confirm-delete"
                                                        data-date="${date}"
                                                        data-route_id="${item.route_id}"
                                                        data-modal="#confirmModal-${uid}"
                                                        data-uid="${uid}">
                                                        Confirm
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>`;
                            });
                        });
                    });

                    $('#mlo-table-body').html(tbody);
                },
                error: function() {
                    $('#mlo-table-body').html(
                        `<tr><td colspan="4" class="text-danger text-center">⚠️ Failed to load data.</td></tr>`
                    );
                }
            });
        }
    </script>
@endpush
