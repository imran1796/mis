@extends('layouts.app', ['activePage' => 'permission', 'title' => 'GLA Admin', 'navName' => 'Permission', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Create New Permission</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('permissions.index') }}"> Back</a>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form id="postForm" method="POST">
                            @method('post')
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Name:</strong>
                                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Name">
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <br>
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
            $('#postForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('permissions.store') }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        $.notify({
                            icon: "add_alert",
                            message: "WOW!!! U Did IT"
                        }, {
                            type: 'success',
                            timer: 2000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            }
                        });

                        // Redirect after success (optional)
                        setTimeout(() => {
                            window.location.href = "{{ route('permissions.index') }}";
                        }, 1000);
                    },
                    error: function(response) {
                        $.notify({
                            icon: "add_alert",
                            message: "Something went wrong!!"
                        }, {
                            type: 'danger',
                            timer: 2000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
