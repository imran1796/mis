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
                            <tbody>
                                {{-- @foreach ($mloWisePerMonth as $date => $items)
                                    @foreach ($items as $item)
                                        @php
                                            $formattedDate = \Carbon\Carbon::parse($date)->format('F Y');
                                            $formId = 'deleteMloWiseByDateForm-' . md5($date . $item->route_id);
                                            $modalId = 'confirmModal-' . md5($date . $item->route_id);
                                        @endphp

                                        <tr class="text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $formattedDate }}</td>
                                            <td>{{ $item->route->name }}</td>
                                            <td>
                                                <!-- Delete Button -->
                                                @can('mloData-delete')
                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                                        data-date="{{ $date }}" data-route-id="{{ $item->route_id }}"
                                                        data-form-id="{{ $formId }}" data-modal-id="{{ $modalId }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan

                                                <!-- Delete Form -->
                                                <form id="{{ $formId }}" class="delete-mlo-form" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="date" value="{{ $date }}">
                                                    <input type="hidden" name="route_id" value="{{ $item->route_id }}">
                                                </form>

                                                <!-- Confirmation Modal -->
                                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="{{ $modalId }}Label"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning p-3">
                                                                <h5 class="modal-title" id="{{ $modalId }}Label">
                                                                    Confirmation Required</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body text-center">
                                                                <h5>Are you sure you want to delete MLO wise count for
                                                                    {{ $formattedDate }}?</h5>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-sm btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success confirmDeleteBtn"
                                                                    data-form-id="{{ $formId }}">
                                                                    Confirm
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach --}}
                                <tbody id="mlo-table-body"></tbody>
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
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            initializeMonthYearPicker('.datepicker');

            $('#recordFromDate, #recordRoutes').on('change', fetchVesselData);


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
                        console.log(response);
                        demo.customShowNotification('success', response.success);
                        window.location.reload();
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

            $('.deleteBtn').on('click', function() {
                const modalId = $(this).data('modal-id');
                $('#' + modalId).modal('show');
            });

            // $('.confirmDeleteBtn').on('click', function() {
            //     const formId = $(this).data('form-id');
            //     const form = $('#' + formId);
            //     const date = form.find('input[name="date"]').val();
            //     const routeId = form.find('input[name="route_id"]').val();
            //     const token = form.find('input[name="_token"]').val();

            //     $.ajax({
            //         url: "{{ route('mloWiseCount.deleteByDateRoute') }}",
            //         method: 'DELETE',
            //         data: {
            //             _token: token,
            //             date: date,
            //             route_id: routeId
            //         },
            //         success: function(response) {
            //             demo.customShowNotification('success', response.success ||
            //                 'Deleted successfully.');
            //             location
            //                 .reload(); // Optionally replace with dynamic row removal for better UX
            //         },
            //         error: function(xhr) {
            //             const res = xhr.responseJSON;
            //             if (res?.error) {
            //                 demo.customShowNotification('danger', res.error);
            //             }
            //             const errors = res?.errors || {};
            //             Object.values(errors).forEach(messages =>
            //                 messages.forEach(msg => demo.customShowNotification('danger',
            //                     msg))
            //             );
            //         }
            //     });
            // });
            $(document).on('click', '.confirm-delete', function() {
                const date = $(this).data('date');
                const route_id = $(this).data('route_id');
                const modalId = $(this).data('modal');

                $.ajax({
                    url: "{{ route('mloWiseCount.deleteByDateRoute') }}",
                    type: 'DELETE',
                    data: {
                        date: date,
                        route_id: route_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // // Close modal
                        // $(modalId).modal('hide');
                        // // Optionally reload or remove row
                        // location.reload(); // or dynamically remove row
                        demo.customShowNotification('success', response.success ||
                            'Deleted successfully.');
                        location
                            .reload();
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
            const routeIds = $('#recordRoutes').val();

            // console.log(fromDate,routeIds);
            $.ajax({
                url: '{{ route('mloWiseCount.perMonth') }}',
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
                                    @can('mloData-delete')
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

                    $('#mlo-table-body').html(tbody);
                },
                error: function() {
                    $('#mlo-table-body').html(
                        '<tr><td colspan="4" class="text-danger text-center">Failed to load data.</td></tr>'
                    );
                }
            });
        }
    </script>
@endpush
