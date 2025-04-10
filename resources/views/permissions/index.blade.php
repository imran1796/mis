@extends('layouts.app', [
    'activePage' => 'permission',
    'title' => 'GLA Admin',
    'navName' => 'Permission',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-between align-items-center">
                        <h2>Permissions</h2>
                        @can('permission-create')
                            {{-- <a class="btn btn-success" href="{{ route('permissions.create') }}">Create New Permission</a> --}}
                            <button class="btn btn-success" data-toggle="modal" data-target="#createPermissionModal">
                                Create New Permission
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            <table class="tableFixHead small-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Guard</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $index => $permission)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->guard_name }}</td>
                                            {{-- <td>
                                                <form action="{{ route('permissions.destroy', $permission->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    @can('permission-delete')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')">Delete</button>
                                                    @endcan
                                                </form>
                                            </td> --}}
                                            <td>
                                                @can('permission-delete')
                                                    <button type="button" class="btn btn-sm btn-danger delete-permission"
                                                        data-id="{{ $permission->id }}">
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                @endcan
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

                @component('components.modal', [
                    'id' => 'createPermissionModal',
                    'title' => 'Create New Permission',
                    'size' => '',
                    'submitButton' => 'savePermissionButton',
                ])
                    <div class="form-group">
                        <label for="name"><strong>Name:</strong></label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm"
                            placeholder="Permission Name">
                    </div>
                @endcomponent


            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#createPermissionModalForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
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
                        $('#createPermissionModal').modal('hide');
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

            $(document).on('click', '.delete-permission', function() {
                let permissionId = $(this).data('id');

                if (!confirm('Are you sure?')) {
                    return;
                }

                $.ajax({
                    url: "{{ route('permissions.destroy', '') }}/" + permissionId,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        demo.customShowNotification('success', response.success);
                        window.location.reload();
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON.error || "Something went wrong!";
                        demo.customShowNotification('danger', errorMessage);
                    }
                });
            });
        });
    </script>
@endpush
