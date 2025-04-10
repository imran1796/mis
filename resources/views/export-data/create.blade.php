@extends('layouts.app', ['activePage' => 'export-data', 'title' => 'GLA Admin', 'navName' => 'Export Data', 'activeButton' => 'laravel'])
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
                                    {{-- <!--
                                            <label class="custom-file-label" for="customFile">Choose file</label>
--> --}}
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="input-name">
                                        <i class="w3-xlarge fa fa-file"></i>{{ __('Date') }}
                                    </label>
                                    <input type="text" name="date" class="form-control form-control-sm datepicker"
                                        placeholder="Select Month and Year" aria-label="Select Month and Year" required>
                                </div>

                                <div class="form-group col-md-12 d-flex justify-content-end">
                                    <button class="btn btn-primary">Import Data</button>

                                    {{-- <!--
                                        <a class="btn btn-success" href="{{ route('export-users') }}">Export Users</a>
--> --}}

                                </div>
                            </div>
                        </form>

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

        
    </script>
@endpush
