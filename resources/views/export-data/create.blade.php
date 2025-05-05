@extends('layouts.app', ['activePage' => 'export-data-create', 'title' => 'GLA Admin', 'navName' => 'Export Data', 'activeButton' => 'laravel'])
@section('content')
    <style>
        .ui-datepicker-calendar {
            display: none;
        }
    </style>

    <div class="">
        <div class="container-fluid">
            <div class="section-image">

                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h3>Export Data</h3>
                        </div>
                    </div>
                </div>
                {{-- <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " --> --}}

                <div class="card">
                    <div class="card-body px-4">
                        <form id="exportDataForm" action="{{ route('export-data.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf


                            @include('alerts.success')
                            @include('alerts.error_self_update', ['key' => 'not_allow_profile'])

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label for="input-name">
                                        <i class="w3-xlarge fa fa-file"></i>{{ __('XLS File') }}
                                    </label>
                                    <input required type="file" name="file" class="form-control form-control-sm"
                                        id="customFile">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="input-name">
                                        <i class="w3-xlarge fa fa-file"></i>{{ __('Date') }}
                                    </label>
                                    <input type="text" name="date" class="form-control form-control-sm datepicker"
                                        placeholder="Select Month and Year" aria-label="Select Month and Year" required>
                                </div>

                                <div class="form-group col-md-12 d-flex justify-content-end">
                                    <button class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="p-0 m-0">Records</h4>
                    </div>
                    <div class="card-body">
                        <table class="tableFixHead table-bordered custom-table-report table-sm">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exportDataMonthly as $item)
                                    @php
                                        $formattedDate = \Carbon\Carbon::parse($item->date)->format('F Y');
                                        $safeDate = \Str::slug($item->date); // e.g., 2024-01-01 becomes 2024-01-01
                                        $modalId = 'confirmModal-' . $safeDate;
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $formattedDate }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                                data-date="{{ $item->date }}" data-modal-id="{{ $modalId }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>

                                            <!-- Confirmation Modal -->
                                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog"
                                                aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning p-3">
                                                            <h5 class="modal-title" id="{{ $modalId }}Label">
                                                                Confirmation Required
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <h5>Are you sure you want to delete export data for
                                                                {{ $formattedDate }}?</h5>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                data-dismiss="modal">Cancel</button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-success confirmDeleteBtn"
                                                                data-date="{{ $item->date }}">
                                                                Confirm
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
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
            initializeMonthYearPicker('.datepicker');

            $('#exportDataForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                // console.log(formData);
                $.ajax({
                    url: '{{ route('export-data.store') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        demo.customShowNotification('success', data.success);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    },

                    error: function(data) {
                        if (data.responseJSON.error) {
                            demo.customShowNotification('danger', data.responseJSON.error);
                        }
                        for (let field in data.responseJSON.errors) {
                            for (let i = 0; i < data.responseJSON.errors[field].length; i++) {
                                demo.customShowNotification('danger', data.responseJSON.errors[
                                    field][i]);
                            }
                        }
                    }
                });
            });

            $('.deleteBtn').on('click', function() {
                const modalId = $(this).data('modal-id');
                $('#' + modalId).modal('show');
            });

            $('.confirmDeleteBtn').on('click', function(e) {
                e.preventDefault();
                const date = $(this).data('date');
                const deleteUrl = '{{ route('export-data.deleteByDate', ':date') }}'.replace(':date',
                    date);
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        demo.customShowNotification('success', response.success);
                        setTimeout(() => {
                            location.reload();
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
    </script>
@endpush
