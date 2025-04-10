@extends('layouts.app', ['activePage' => 'role', 'title' => 'GLA Admin', 'navName' => 'Role', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Role Management</h2>
                        </div>
                        <div class="pull-right">
                            @can('role-create')
                                <a class="btn btn-success" href="{{ route('roles.create') }}"> Create New Role</a>
                            @endcan
                        </div>
                    </div>
                </div>


                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif


                <div class="card">
                    <div class="card-body">
                        <table class="tableFixHead table-bordered small-table">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th width="280px">Action</th>
                            </tr>

                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        {{-- <a class="btn btn-sm btn-info" href="{{ route('roles.show', $role->id) }}">Show</a> --}}
                                        @can('role-edit')
                                            <a class="btn btn-sm btn-primary"
                                                href="{{ route('roles.edit', $role->id) }}"><i class='fas fa-edit'></i></a>
                                        @endcan
                                        {{-- @can('role-delete')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                {!! $roles->render() !!}
            </div>
        </div>
    </div>
@endsection
