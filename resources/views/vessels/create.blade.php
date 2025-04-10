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
                            <h2>Upload Vessels</h2>
                        </div>

                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <form id="vesselUploadForm" action="{{route('vessels.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2 text-center">
                                    <p><i class="fa fa-file"></i>{{ __(' XLS File') }}</p>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input required type="file" name="file" class="form-control form-control-sm">
                                </div>


                                <div class="form-group col-sm-2 text-center">
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
