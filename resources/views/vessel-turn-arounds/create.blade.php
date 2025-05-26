@extends('layouts.app', [
    'activePage' => 'vesselTurnAround-create',
    'title' => 'GLA Admin',
    'navName' => 'Vessel Turnaround',
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
                        <h2>Vessel Turnaround Upload</h2>
                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"><i class="fas fa-calendar-alt"></i> Date</label>
                                <div class="col-sm-10">
                                    <input type="text" name="date" class="form-control form-control-sm datepicker"
                                        placeholder="Select Month and Year" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"><i class="fas fa-file-excel"></i> XLS File</label>
                                <div class="col-sm-10">
                                    <input type="file" name="file" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>Uploaded Records</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($turnAroundByMonth as $date => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('F Y') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                                data-date="{{ $date }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <form id="delete-form-{{ $date }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="date" value="{{ $date }}">
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No data available</td>
                                    </tr>
                                @endforelse
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

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('vesselTurnAround.store') }}',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success(response) {
                        demo.customShowNotification('success', response.success);
                        window.location.reload();
                    },
                    error(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.error) {
                            demo.customShowNotification('danger', res.error);
                        }
                        if (res?.errors) {
                            Object.values(res.errors).flat().forEach(msg => {
                                demo.customShowNotification('danger', msg);
                            });
                        }
                    }
                });
            });

            $('.deleteBtn').on('click', function() {
                const date = $(this).data('date');
                if (!confirm('Are you sure you want to delete records for this date?')) return;

                const form = $('#delete-form-' + date);

                $.ajax({
                    url: '{{ route('vesselTurnAround.deleteByDate') }}',
                    method: 'DELETE',
                    data: form.serialize(),
                    success(response) {
                        demo.customShowNotification('success', response.success);
                        window.location.reload();
                    },
                    error(xhr) {
                        const res = xhr.responseJSON;
                        if (res?.error) {
                            demo.customShowNotification('danger', res.error);
                        }
                        if (res?.errors) {
                            Object.values(res.errors).flat().forEach(msg => {
                                demo.customShowNotification('danger', msg);
                            });
                        }
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
