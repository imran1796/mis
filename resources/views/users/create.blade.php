@extends('layouts.app', ['activePage' => 'user', 'title' => 'GLA Admin', 'navName' => 'User', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">

                    <div class="col-lg-12 margin-tb">

                        <div class="pull-left">

                            <h2>Create New User</h2>

                        </div>

                    </div>

                </div>


                @if (count($errors) > 0)
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
                    <div class="card-body px-4">

                        <form id="createUserForm">
                            @csrf

                            <div class="row">
                                <p class="col-sm-2 "><strong>Name:</strong></p>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control form-control-sm">

                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2 "><strong>Email:</strong></p>
                                <div class="col-sm-10">
                                    <input type="email" name="email" class="form-control form-control-sm">

                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2 "><strong>Password:</strong></p>
                                <div class="col-sm-10">
                                    <input type="password" name="password" class="form-control form-control-sm">

                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2 "><strong>Confirm Password:</strong></p>
                                <div class="col-sm-10">
                                    <input type="password" name="confirm-password" class="form-control form-control-sm">
                                </div>
                            </div>

                            {{-- multiselect-search="true"
                            multiselect-max-items="3"
                            multiselect-select-all="true"
                            multiselect-hide-x="true" --}}
                            <div class="row mb-2">
                                <p class="col-sm-2 "><strong>Role:</strong></p>
                                <div class="col-sm-10">
                                    <select name="roles[]" class="form-control form-control-sm selectpicker" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}">{{ $role }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            <div class="text-end pull-right">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
            $('#createUserForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('users.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        console.log(response);
                        demo.customShowNotification('success', response.success);
                        setTimeout(() => {
                            window.location.href = "{{ route('users.index') }}";
                        }, 2000);
                    },
                    error: function(response) {
                        console.log(response);
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
