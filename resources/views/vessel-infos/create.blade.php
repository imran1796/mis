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
                            <div class="col-md-8">
                                <h4>Records</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <input type="text" id="recordFromDate" name="from_date"
                                            class="form-control form-control-sm datepicker" placeholder="From Month"
                                            value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <select name="route_id[]" id="recordRoutes"
                                            class="form-control form-control-sm selectpicker" title="Route" multiple>
                                            @foreach ($routes as $route)
                                                <option value="{{ $route->id }}"
                                                    {{ collect(request('route_id'))->contains($route->id) ? 'selected' : '' }}>
                                                    {{ $route->name }}</option>
                                            @endforeach
                                        </select>
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
                        location.reload();
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

            $('#recordFromDate, #recordRoutes').on('change', fetchVesselData);

            // $('.confirm-delete').on('click', function () {
            $(document).on('click', '.confirm-delete', function() {
                const date = $(this).data('date');
                const route_id = $(this).data('route_id');
                const modalId = $(this).data('modal');

                $.ajax({
                    url: "{{ route('vesselInfo.deleteByDateRoute') }}",
                    type: 'DELETE',
                    data: {
                        date: date,
                        route_id: route_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Close modal
                        $(modalId).modal('hide');
                        // Optionally reload or remove row
                        location.reload(); // or dynamically remove row
                    },
                    error: function(xhr) {
                        alert('Delete failed: ' + xhr.responseText);
                        $(modalId).modal('hide');
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
            const routeIds = $('#recordRoutes').val();

            $.ajax({
                url: '{{ route('vesselInfo.perMonth') }}',
                method: 'GET',
                data: {
                    date: fromDate,
                    route_id: routeIds
                },
                success: function(response) {
                    let tbody = '';
                    let i = 1;

                    Object.keys(response).forEach(date => {
                        response[date].forEach(item => {
                            const uid = `${date}${item.route_id}`;
                            var formattedDate = new Date(item.date);
                            formattedDate = formattedDate.toLocaleString('en-US', {
                                month: 'long',
                                year: 'numeric'
                            });
                            // console.log(item.date,formattedDate);
                            tbody += `
                            <tr class="text-center">
                                <td>${i++}</td>
                                <td>${formattedDate}</td>
                                <td>${item.route.name}</td>
                                <td>
                                    @can('operatorData-delete')
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmModal-${uid}"><i class="fas fa-trash"></i></button>
                                    @endcan
                                    <div class="modal fade" id="confirmModal-${uid}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning p-3">
                                                    <h5 class="modal-title">Confirmation Required</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <h5>Are you sure you want to delete vessel-wise data for ${formattedDate}?</h5>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-sm btn-success confirm-delete" 
                                                        data-date="${date}" 
                                                        data-route_id="${item.route_id}" 
                                                        data-modal="#confirmModal-${uid}">
                                                        Confirm
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        });
                    });

                    $('#vessel-table-body').html(tbody);
                },
                error: function() {
                    $('#vessel-table-body').html(
                        '<tr><td colspan="4" class="text-danger text-center">Failed to load data.</td></tr>'
                    );
                }
            });
        }
    </script>
@endpush
