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
                            <h2>Mlo</h2>
                        </div>

                        <div class="pull-right">
                            {{-- @can('mlos.create') --}}
                            {{-- <button class="btn btn-success" id="mloCreateBtn" href="{{ route('mlos.create') }}">Add Vessel</button> --}}
                            {{-- <a class="btn btn-success" href="{{ route('mlos.create') }}">Upload Mlo</a> --}}
                            <button class="btn btn-success" data-toggle="modal" data-target="#createMloModal">
                                Add New MLO
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
                                    <th>Line Belongs To</th>
                                    <th>MLO Code</th>
                                    <th>MLO Details</th>
                                    <th>Effective To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mlos as $mlo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $mlo->line_belongs_to }}</td>
                                        <td>{{ $mlo->mlo_code }}</td>
                                        <td>{{ $mlo->mlo_details }}</td>
                                        <td>{{ $mlo->effective_to??'' }}</td>
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
                        <div class="form-group">
                            <label for="line_belongs_to"><strong>Line Belongs To:</strong></label>
                            <input type="text" name="line_belongs_to" id="line_belongs_to"
                                class="form-control form-control-sm" placeholder="Line Belongs To">
                        </div>
                        <div class="form-group">
                            <label for="mlo_code"><strong>Mlo Code:</strong></label>
                            <input type="text" name="mlo_code" id="mlo_code" class="form-control form-control-sm"
                                placeholder="MLO Code">
                        </div>
                        <div class="form-group">
                            <label for="mlo_details"><strong>Mlo Details:</strong></label>
                            <input type="text" name="mlo_details" id="mlo_details" class="form-control form-control-sm"
                                placeholder="MLO Details">
                        </div>
                        {{-- <div class="form-group">
                            <label for="effective_from"><strong>Effective From(option):</strong></label>
                            <input type="date" name="effective_from" id="effective_from" class="form-control form-control-sm"
                                placeholder="Effective from">
                        </div> --}}
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
        });
    </script>
@endpush
