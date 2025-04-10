@extends('layouts.app', ['activePage' => 'user', 'title' => 'GLA Admin', 'navName' => 'User', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Edit User</h2>
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
                        <form id="editUserForm">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                            <div class="row">
                                <p class="col-sm-2"><strong>Name:</strong></p>
                                <div class="col-sm-10">
                                    <input type="text" name="name" value="{{ $user->name }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2"><strong>Email:</strong></p>
                                <div class="col-sm-10">
                                    <input type="email" name="email" value="{{ $user->email }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2"><strong>Password:</strong></p>
                                <div class="col-sm-10">
                                    <input type="password" name="password" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row">
                                <p class="col-sm-2"><strong>Confirm Password:</strong></p>
                                <div class="col-sm-10">
                                    <input type="password" name="confirm-password" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <p class="col-sm-2"><strong>Role:</strong></p>
                                <div class="col-sm-10">
                                    <select name="roles[]" class="form-control form-control-sm selectpicker" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" 
                                                {{ in_array($role, $userRoles) ? 'selected' : '' }}>
                                                {{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="text-end pull-right">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
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
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let userId = $('input[name="user_id"]').val();
                
                $.ajax({
                    url: "{{ url('users') }}/" + userId,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                        'X-HTTP-Method-Override': 'PUT' // Since Laravel requires PUT
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
                            for (let i = 0; i < response.responseJSON.errors[field].length; i++) {
                                demo.customShowNotification('danger', response.responseJSON.errors[field][i]);
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
