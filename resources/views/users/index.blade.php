@extends('layouts.app', ['activePage' => 'user', 'title' => 'GLA Admin', 'navName' => 'User', 'activeButton' => 'laravel'])
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">

                    <div class="col-lg-12 margin-tb">

                        <div class="pull-left">

                            <h2>Users Management</h2>

                        </div>

                        <div class="pull-right">

                            <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>

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

                                <th>Email</th>

                                <th>Roles</th>

                                <th width="280px">Action</th>

                            </tr>

                            @foreach ($users as $key => $user)
                                @if (!$user->hasRole('System Admin') || \Illuminate\Support\Facades\Auth::user()->hasRole('System Admin'))
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if (!empty($user->getRoleNames()))
                                                @foreach ($user->getRoleNames() as $v)
                                                    <h6 class="badge badge-success text-white">{{ $v }}</h6>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('users.edit', $user->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        
                                            {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> --}}
                                            {{-- <!-- Delete Button -->
                                        
                                            <!-- Verify Button or Verified Status -->
                                            @if (is_null($user->email_verified_at))
                                                <form action="{{ route('users.verify', $user->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Verify User">
                                                        <i class="fa fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn btn-sm btn-success" title="Verified">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @endif --}}
                                        </td>
                                        

                                    </tr>
                                @endif
                            @endforeach

                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection
