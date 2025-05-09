@extends('layouts.app', [
    'activePage' => 'mlo',
    'title' => 'GLA Admin',
    'navName' => 'Mlo',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Upload Mlos</h2>
                        </div>

                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <form id="vesselUploadForm" action="{{ route('mlos.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-2 text-center">
                                    <p><i class="fa fa-file"></i>{{ __(' XLS File') }}</p>
                                </div>
                                <div class="form-group col-md-8">
                                    <input required type="file" name="file" class="form-control form-control-sm">
                                </div>


                                <div class="form-group col-md-2 text-center">
                                    <button type="submit" class="btn btn-sm btn-primary">Upload Vessels</button>
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
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush