@extends('layouts.app', [
    'activePage' => 'vessel',
    'title' => 'GLA Admin',
    'navName' => 'Vessel',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Vessel</h2>
                        </div>

                        <div class="pull-right">
                            {{-- @can('vessels.create') --}}
                            {{-- <button class="btn btn-success" id="vesselCreateBtn" href="{{ route('vessels.create') }}">Add Vessel</button>
                            <a class="btn btn-success" href="{{ route('vessels.create') }}">Upload Vessels</a> --}}
                            <button class="btn btn-success" data-toggle="modal" data-target="#createVesselModal">
                                Create New Vessel
                            </button>
                            {{-- @endcan --}}
                        </div>
                    </div>
                </div>

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
                                    <th>VSL Name</th>
                                    <th>LOA</th>
                                    <th>Crane Status</th>
                                    <th>NOM CAPACITY</th>
                                    <th>IMO NUM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vessels as $vessel)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vessel->vessel_name }}</td>
                                        <td>{{ $vessel->length_overall }}</td>
                                        <td>{{ $vessel->crane_status }}</td>
                                        <td>{{ $vessel->nominal_capacity }}</td>
                                        <td>{{ $vessel->imo_no }}</td>
                                    </tr>
                                @endforeach
                            </tbody>


                        </table>

                        @component('components.modal', [
                            'id' => 'createVesselModal',
                            'title' => 'Create New Vessel',
                            'size' => '',
                            'submitButton' => 'saveVesselButton',
                        ])
                            <div class="form-group">
                                <label for="vessel_name"><strong>Vessel Name:</strong></label>
                                <input type="text" name="vessel_name" id="vessel_name" class="form-control form-control-sm"
                                    placeholder="Vessel Name">
                            </div>
                            <div class="form-group">
                                <label for="length_overall"><strong>Length Overall:</strong></label>
                                <input type="text" name="length_overall" id="length_overall"
                                    class="form-control form-control-sm" placeholder="Length Overall">
                            </div>
                            <div class="form-group">
                                <label for="creane_status"><strong>Crane Status:</strong></label>
                                <select name="crane_status" id="crane_status" class="form-control form-control-sm">
                                    <option value="">Select Crane Status</option>
                                    <option value="G">G</option>
                                    <option value="GL">GL</option>
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="nominal_capacity"><strong>Nominal Capacity:</strong></label>
                                <input type="text" name="nominal_capacity" id="nominal_capacity"
                                    class="form-control form-control-sm" placeholder="Nominal Capacity">
                            </div>
                            <div class="form-group">
                                <label for="imo_no"><strong>IMO NO:</strong></label>
                                <input type="text" name="imo_no" id="imo_no" class="form-control form-control-sm"
                                    placeholder="IMO No">
                            </div>
                        @endcomponent
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

            $('#createVesselModalForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('vessels.store') }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        $('#createVesselModal').modal('hide');
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
        });
    </script>
@endpush
