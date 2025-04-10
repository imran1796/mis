@extends('layouts.app', ['activePage' => 'role', 'title' => 'GLA Admin', 'navName' => 'Role', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">

                    <div class="col-lg-12 margin-tb">

                        <div class="pull-left">

                            <h2>Edit Role</h2>

                        </div>

                        <div class="pull-right">

                            <a class="btn btn-primary" href="{{ route('roles.index') }}"> Back</a>

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


                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row">

                        <div class="col-xs-12 col-sm-12 col-md-12 mb-3">
                            <div class="form-group row">
                                <div class="col-sm-1"><strong>Role:</strong></div>
                                <div class="col-sm-11"><input type="text" name="name" value="{{ old('name', $role->name) }}"
                                    class="form-control form-control-sm" placeholder="Name"></div>
                                
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Permission:</strong>
                                <br />

                                <div class="card-columns">
                                    @foreach ($groupedPermission as $key => $permissions)
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-1">{{ ucfirst($key) }}</h6>
                                            </div>
                                            <hr class="p-0 m-0">
                                            <div class="card-body">
                                                @foreach ($permissions as $permission)
                                                    <label style="text-transform: none;">
                                                        <input type="checkbox" name="permission[]"
                                                            value="{{ $permission->name }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                            class="name">
                                                        {{ $permission->name }}
                                                    </label>
                                                    <br />
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
