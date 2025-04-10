@extends('layouts.app', ['activePage' => 'role', 'title' => 'GLA Admin', 'navName' => 'Role', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">

                    <div class="col-lg-12 margin-tb">

                        <div class="pull-left">

                            <h2>Create New Role</h2>

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


                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" class="form-control form-control-sm"
                                    placeholder="Name">
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
