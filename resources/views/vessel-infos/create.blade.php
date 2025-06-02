@extends('layouts.app', [
    'activePage' => 'vesselInfo-create',
    'title' => 'GLA Admin',
    'navName' => 'Vessel',
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
                    <div class="col-lg-12">
                        <h2>Operator Wise Data Upload</h2>
                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="form">
                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <label for="route_id" class="form-label">Route</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <select name="route_id" id="route_id" class="form-control" required>
                                            @foreach ($routes as $route)
                                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <label><i class="w3-xlarge fa fa-calendar"></i> Date</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="text" name="date" class="form-control form-control-sm datepicker"
                                            placeholder="Select Month and Year" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <label><i class="w3-xlarge fa fa-file"></i> XLS File</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input type="file" name="file" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">Upload</button>
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
                                    <th>Report</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="vessel-table-body"></tbody>
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
            initializeMonthYearPicker('.datepicker');

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('vesselInfo.store') }}',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                        let res = response.responseJSON;
                        if (res?.error) demo.customShowNotification('danger', res.error);
                        if (res?.errors) {
                            Object.values(res.errors).forEach(msgs => msgs.forEach(msg => demo
                                .customShowNotification('danger', msg)));
                        }
                    }
                });
            });

            $('#searchVesselDataBtn').on('click', function() {
                fetchVesselData();
            });

            // $('#recordFromDate, #recordRoutes').on('change', fetchVesselData);

            // $('.confirm-delete').on('click', function () {
            $(document).on('click', '.confirm-delete', function() {
                const date = $(this).data('date');
                const route_id = $(this).data('route_id');
                const modalId = $(this).data('modal');
                const uid = $(this).data('uid');

                console.log(date, route_id, modalId, uid);

                $.ajax({
                    url: "{{ route('vesselInfo.deleteByDateRoute') }}",
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
                onChangeMonthYear: function(y, m, i) {
                    $(this).datepicker('setDate', new Date(y, m - 1, 1));
                },
                onClose: function() {
                    const month = $('#ui-datepicker-div .ui-datepicker-month :selected').val();
                    const year = $('#ui-datepicker-div .ui-datepicker-year :selected').val();
                    if (month !== null && year !== null) {
                        $(this).datepicker('setDate', new Date(year, month, 1));
                    }
                },
                beforeShow: function(input) {
                    const val = $(input).val();
                    if (val.length > 0) {
                        const year = val.slice(-4);
                        const month = $.inArray(val.slice(0, -5), $(input).datepicker('option', 'monthNames'));
                        if (month !== -1) {
                            $(input).datepicker('option', 'defaultDate', new Date(year, month, 1));
                            $(input).datepicker('setDate', new Date(year, month, 1));
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
                url: '{{ route('vesselInfo.perMonth') }}',
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
                                const baseDownloadUrl = @json(route('reports.operator.container-handling'));

                                tbody += `
                            <tr class="text-center" data-uid="${uid}">
                                <td>${i++}</td>
                                <td>${formattedDate}</td>
                                <td>${item.route?.name || 'N/A'}</td>
                                <td>
                                    <a href="${baseDownloadUrl}?date=${item.date}&route_id=${item.route_id}"
                                        class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i>
                                    </a>
                                </td>
                                <td>
                                    @can('operatorData-delete')
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

                    $('#vessel-table-body').html(tbody);
                },
                error: function() {
                    $('#vessel-table-body').html(
                        `<tr><td colspan="4" class="text-danger text-center">⚠️ Failed to load data.</td></tr>`
                    );
                }
            });
        }
    </script>
@endpush
