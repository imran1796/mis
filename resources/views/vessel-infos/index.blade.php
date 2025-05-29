@extends('layouts.app', [
    'activePage' => 'vesselInfo-index',
    'title' => 'GLA Admin',
    'navName' => 'Vessel Info',
    'activeButton' => 'laravel',
])

@section('content')
    <style>
        .ui-timepicker-container {
            z-index: 9999 !important;
        }
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
                            <h2>Vessel-Opt Wise Data</h2>
                        </div>


                    </div>
                </div>

                <form class="row ">

                    {{-- <div class="col-sm-2 pr-0 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input placeholder="From Date" class="form-control form-control-sm datepicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div> --}}

                    <div class="col-sm-2 pr-0 mt-1 form-group">
                        <label for="from_date" class="sr-only">From Date</label>
                        <input placeholder="From Date" class="form-control form-control-sm monthpicker" type="text"
                            name="from_date" id="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class=" col-sm-2 mt-1 form-group">
                        <label for="to_date" class="sr-only">To Date</label>
                        <input placeholder="To Date" class="form-control form-control-sm monthpicker" type="text"
                            name="to_date" id="to_date" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2 px-1 form-group">
                        <select id="pod" name="route_id[]" class="form-control form-control-sm selectpicker" multiple
                            title="Select Route">
                            @foreach ($pods as $pod)
                                <option value="{{ $pod->id }}"
                                    {{ in_array($pod->id, (array) request('route_id')) ? 'selected' : '' }}>
                                    {{ $pod->name }}</option>
                            @endforeach
                        </select>
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
                        <table class="tableFixHead table-bordered table2excel custom-table-report mb-3 table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vessel</th>
                                    <th>Rotation</th>
                                    <th>Jetty</th>
                                    <th>Operator</th>
                                    <th>Local Agent</th>
                                    <th>Nom Cap.</th>
                                    <th>Eff Cap.</th>
                                    <th>LOA</th>
                                    <th>Crane</th>
                                    <th>IMO No.</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $dt)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $dt->vessel->vessel_name }} </td>
                                        <td>{{ $dt->rotation_no }} </td>
                                        <td>{{ $dt->jetty }} </td>
                                        <td>{{ $dt->operator }} </td>
                                        <td>{{ $dt->local_agent }}</td>
                                        <td>{{ $dt->vessel->nominal_capacity }}</td>
                                        <td>{{ $dt->effective_capacity }} </td>
                                        <td>{{ $dt->vessel->length_overall }}</td>
                                        <td>{{ $dt->vessel->crane_status }}</td>
                                        <td>{{ $dt->vessel->imo_no }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success editVesselInfoBtn"
                                                data-id="{{ $dt->id }}"
                                                data-vessel_name="{{ $dt->vessel->vessel_name }}"
                                                data-rotation_no="{{ $dt->rotation_no ?? '' }}"
                                                data-jetty="{{ $dt->jetty ?? '' }}"
                                                data-operator="{{ $dt->operator ?? '' }}"
                                                data-local_agent="{{ $dt->local_agent ?? '' }}"
                                                data-berth_date="{{ $dt->berth_date ?? '' }}"
                                                data-arrival_date="{{ $dt->arrival_date ?? '' }}"
                                                data-sail_date="{{ $dt->sail_date ?? '' }}"
                                                data-berth_time="{{ $dt->berth_time ?? '' }}"
                                                data-arrival_time="{{ $dt->arrival_time ?? '' }}"
                                                data-sail_time="{{ $dt->sail_time ?? '' }}"
                                                data-effective_capacity="{{ $dt->effective_capacity ?? '' }}"
                                                data-target="#editVesselInfoModal" data-toggle="modal">
                                                Edit
                                            </button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                @component('components.modal', [
                    'id' => 'editVesselInfoModal',
                    'title' => 'Edit Vessel Info',
                    'size' => 'modal-md',
                    'submitButton' => 'editVesselInfoButton',
                ])
                    <input type="hidden" name="vessel_info_id" id="vessel_info_id">

                    <div class="form-group row">
                        <div class="col-sm-3"><label for="rotation_no"><strong>Rot No:</strong></label></div>
                        <div class="col-sm-9"><input type="text" name="rotation_no" id="rotation_no"
                                class="form-control form-control-sm" placeholder="Rotation No"></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3"><label for="jetty"><strong>Jetty:</strong></label></div>
                        <div class="col-sm-9"><input type="text" name="jetty" id="jetty"
                                class="form-control form-control-sm" placeholder="Jetty"></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3"><label for="operator"><strong>Opt:</strong></label></div>
                        <div class="col-sm-9"><input type="text" name="operator" id="operator"
                                class="form-control form-control-sm" placeholder="Operator"></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3"><label for="local_agent"><strong>L.Agent:</strong></label></div>
                        <div class="col-sm-9"><input type="text" name="local_agent" id="local_agent"
                                class="form-control form-control-sm" placeholder="Local Agent"></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3"><label for="effective_capacity"><strong>Eff Cap:</strong></label>
                        </div>
                        <div class="col-sm-9"><input type="number" name="effective_capacity" id="effective_capacity"
                                class="form-control form-control-sm" placeholder="Effective Capacity"></div>

                    </div>


                    <div class="form-group">
                        <label for=""><strong>Arrival Date/Time</strong></label>
                        <div class="row">
                            <div class="col-sm-6"><input type="text" step="0.1" name="arrival_date"
                                    id="arrival_date" class="form-control form-control-sm datepicker"
                                    placeholder="Arrival Date">
                            </div>
                            <div class="col-sm-6"><input type="text" step="0.1" name="arrival_time"
                                    id="arrival_time" class="form-control form-control-sm timepicker"
                                    placeholder="Arrival Time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=""><strong>Berth Date/Time</strong></label>
                        <div class="row">
                            <div class="col-sm-6"><input type="text" step="0.1" name="berth_date" id="berth_date"
                                    class="form-control form-control-sm datepicker" placeholder="Berth Date">
                            </div>
                            <div class="col-sm-6"><input type="text" step="0.1" name="berth_time" id="berth_time"
                                    class="form-control form-control-sm timepicker" placeholder="Berth Time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=""><strong>Sail Date/Time</strong></label>
                        <div class="row">
                            <div class="col-sm-6"><input type="text" step="0.1" name="sail_date" id="sail_date"
                                    class="form-control form-control-sm datepicker" placeholder="Sail Date">
                            </div>
                            <div class="col-sm-6"><input type="text" step="0.1" name="sail_time" id="sail_time"
                                    class="form-control form-control-sm timepicker" placeholder="Sail Time">
                            </div>
                        </div>
                    </div>
                @endcomponent

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

            initializeMonthYearPicker('.monthpicker');

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

            $('.timepicker').timepicker({
                timeFormat: 'HH:mm:ss',
                showSeconds: true,
                showMeridian: false,
                defaultTime: false
            });


            $('.editVesselInfoBtn').on('click', function() {
                $('#vessel_info_id').val($(this).data('id'));
                $('#rotation_no').val($(this).data('rotation_no'));
                $('#jetty').val($(this).data('jetty'));
                $('#operator').val($(this).data('operator'));
                $('#local_agent').val($(this).data('local_agent'));
                $('#effective_capacity').val($(this).data('effective_capacity'));
                $('#berth_date').val($(this).data('berth_date'));
                $('#arrival_date').val($(this).data('arrival_date'));
                $('#sail_date').val($(this).data('sail_date'));
                $('#berth_time').val($(this).data('berth_time'));
                $('#arrival_time').val($(this).data('arrival_time'));
                $('#sail_time').val($(this).data('sail_time'));
            });

            // Submit Modal Form
            $('#editVesselInfoModalForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                // console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('vesselInfo.update') }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        $('#editVesselInfoModal').modal('hide');
                        demo.customShowNotification('success', response.success);
                        window.location.reload();
                    },
                    error: function(response) {
                        if (response.responseJSON?.error) {
                            demo.customShowNotification('danger', response.responseJSON.error);
                        }
                        const errors = response.responseJSON?.errors || {};
                        Object.keys(errors).forEach(field => {
                            errors[field].forEach(msg => {
                                demo.customShowNotification('danger', msg);
                            });
                        });
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
                    const dateExist = $('.datepicker').val();
                    // // const tDateExist = $('.tdatepicker').val();
                    // console.log(dateExist);

                    if (iMonth !== null && iYear !== null && dateExist != '') {
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
