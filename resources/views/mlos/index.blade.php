@extends('layouts.app', [
    'activePage' => 'mlo',
    'title' => 'GLA Admin',
    'navName' => 'MLO',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>MLO</h2>
                        </div>

                        <div class="pull-right">
                            {{-- @can('mlos.create') --}}
                            {{-- <button class="btn btn-success" id="mloCreateBtn" href="{{ route('mlos.create') }}">Add Vessel</button> --}}
                            {{-- <a class="btn btn-success" href="{{ route('mlos.create') }}">Upload Mlo</a> --}}
                            @can('mlo-list')
                                <button class="btn btn-success" data-toggle="modal" data-target="#createMloModal">
                                Add New MLO
                            </button>
                            @endcan
                            
                            {{-- @endcan --}}
                        </div>
                    </div>
                </div>

                <form class="row ">

                    <div class="col-sm-2 pr-0 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input placeholder="From Date" class="form-control form-control-sm datepicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-sm-2 mt-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input placeholder="To Date" class="form-control form-control-sm datepicker" type="text"
                            name="to_date" id="to_date" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-sm-2 pr-0 mt-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Search</button>
                    </div>
                    <!-- <div class="col-sm-2 pr-0 mt-1">
                                                                                                    <button class="btn btn-success btn-sm w-100" id="btnExcelJsExport" type="button"><i class="fa fa-download"
                                                                                                            aria-hidden="true"></i> xls</button>
                                                                                                </div> -->
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
                        <table class="tableFixHead table-bordered table2excel custom-table-report mb-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Line Belongs To</th>
                                    <th>MLO Code</th>
                                    <th>MLO Details</th>
                                    <th>Effective To</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mlos as $mlo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $mlo->line_belongs_to }}</td>
                                        <td>{{ $mlo->mlo_code }}</td>
                                        <td>{{ $mlo->mlo_details }}</td>
                                        <td>{{ $mlo->effective_to ?? '' }}</td>
                                        <td>
                                            @can('mlo-list')
                                                <button class="btn btn-sm btn-success editMloInfoBtn"
                                                    data-id="{{ $mlo->id }}"
                                                    data-line_belongs_to="{{ $mlo->line_belongs_to ?? '' }}"
                                                    data-mlo_code="{{ $mlo->mlo_code ?? '' }}"
                                                    data-mlo_details="{{ $mlo->mlo_details ?? '' }}"
                                                    data-effective_from="{{ $mlo->effective_from ?? '' }}"
                                                    data-effective_to="{{ $mlo->effective_to ?? '' }}"
                                                    data-is_show_nvocc="{{ $mlo->is_show_nvocc ?? '' }}"
                                                    data-target="#editMloModal" data-toggle="modal">
                                                    Edit
                                                </button>
                                            @endcan
                                            
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>


                        </table>
                    </div>

                    @component('components.modal', [
                        'id' => 'createMloModal',
                        'title' => 'Create New Mlo',
                        'size' => '',
                        'submitButton' => 'saveMloButton',
                    ])
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Line Blngs To:</strong></div>
                            <div class="col-sm-9">
                                <input type="text" name="line_belongs_to" id="line_belongs_to"
                                    class="form-control form-control-sm" placeholder="Line Belongs To">
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Mlo Code:</strong></div>
                            <div class="col-sm-9">
                                <input type="text" name="mlo_code" id="mlo_code" class="form-control form-control-sm"
                                    placeholder="MLO Code">
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Mlo Details:</strong></div>
                            <div class="col-sm-9">
                                <input type="text" name="mlo_details" id="mlo_details" class="form-control form-control-sm"
                                    placeholder="MLO Details">
                            </div>

                        </div>
                        {{-- <div class="form-group">
                            <label for="effective_from"><strong>Effective From(option):</strong></label>
                            <input type="date" name="effective_from" id="effective_from" class="form-control form-control-sm"
                                placeholder="Effective from">
                        </div> --}}
                    @endcomponent

                    @component('components.modal', [
                        'id' => 'editMloModal',
                        'title' => 'Edit New Mlo',
                        'size' => '',
                        'submitButton' => 'updateMloButton',
                        'method' => 'PUT',
                    ])
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Line Blngs To:</strong></div>
                            <div class="col-sm-9"><input type="text" name="line_belongs_to" id="e_line_belongs_to"
                                    class="form-control form-control-sm" placeholder="Line Belongs To"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Mlo Code:</strong></div>
                            <div class="col-sm-9"><input type="text" name="mlo_code" id="e_mlo_code"
                                    class="form-control form-control-sm" placeholder="MLO Code"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Mlo Details:</strong></div>
                            <div class="col-sm-9"><input type="text" name="mlo_details" id="e_mlo_details"
                                    class="form-control form-control-sm" placeholder="MLO Details"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Effective From</strong></div>
                            <div class="col-sm-9"><input type="date" name="effective_from" id="e_effective_from"
                                    class="form-control form-control-sm datepicker" placeholder="Effective from"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Effective To</strong></div>
                            <div class="col-sm-9"><input type="date" name="effective_to" id="e_effective_to"
                                    class="form-control form-control-sm datepicker" placeholder="Effective to"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"><strong>Show In NVOCC Report</strong></div>
                            <div class="col-sm-9">
                                <select name="is_show_nvocc" id="e_show_in_nvocc" class="form-control form-control-sm">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        
                    @endcomponent
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

            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd',
                showButtonPanel: true,
                currentText: "Today",

                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        var buttonPane = $(inst.dpDiv).find('.ui-datepicker-buttonpane');

                        buttonPane.find('.ui-datepicker-current').off('click').on('click',
                            function() {
                                var today = new Date();
                                $(input).datepicker('setDate', today);
                                $.datepicker._hideDatepicker(input); //close after selecting
                                $(input).blur(); //prevent auto-focus/reopen
                            });
                    }, 1);
                }
            });

            $('#createMloModalForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('mlos.store') }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        $('#createMloModal').modal('hide');
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

            $('.editMloInfoBtn').on('click', function() {
                var mloId = $(this).data('id');

                $('#editMloModal').data('mlo-id', mloId);

                $('#e_line_belongs_to').val($(this).data('line_belongs_to'));
                $('#e_mlo_code').val($(this).data('mlo_code'));
                $('#e_mlo_details').val($(this).data('mlo_details'));
                $('#e_effective_from').val($(this).data('effective_from'));
                $('#e_effective_to').val($(this).data('effective_to'));
                $('#e_show_in_nvocc').val($(this).data('is_show_nvocc'));
            });


            $('#editMloModalForm').on('submit', function(e) {
                e.preventDefault();

                let mloId = $('#editMloModal').data('mlo-id');

                let formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('mlos.update', ':mlo') }}'.replace(':mlo',
                        mloId),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        $('#editMloModal').modal('hide');
                        demo.customShowNotification('success', response.success);
                        window.location
                            .reload();
                    },
                    error: function(response) {
                        if (response.responseJSON.error) {
                            demo.customShowNotification('danger', response.responseJSON.error);
                        }
                        if (response.responseJSON.errors) {
                            $.each(response.responseJSON.errors, function(field, messages) {
                                messages.forEach(function(message) {
                                    demo.customShowNotification('danger',
                                        message);
                                });
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
