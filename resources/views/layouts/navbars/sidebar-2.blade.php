<nav id="sidebar">
    <div class="py-2 px-3">
        <div class="col-md-12 mb-2" style="width: 20%;">
            <img class="shadow-img" style="width:180px; height: 100px" src="{{ asset('storage/4.png') }}">
        </div>
        <ul class="list-unstyled components mb-5">

            <li class="nav-item @if ($activePage == 'dashboard') active @endif">

                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    {{ __('Dashboard') }}
                </a>
            </li>

            {{-- @canany(['configuration-list', 'user-list', 'role-list', 'permission-list', 'vendor-list', 'depot-list', 'commodity-list', 'usd-list', 'company-list', 'branch-list', 'portList-list', 'carrier-list', 'principle-list', 'charge-update', 'local-charge-index']) --}}
            {{-- @can('general-section') --}}
            <li class="">
                <a href="#general" data-toggle="collapse"
                    aria-expanded="{{ in_array($activePage, ['user', 'role', 'permission']) ? 'true' : 'false' }}"
                    class="dropdown-toggle">
                    <i class="fas fa-bars"></i>
                    General</a>
                <ul class="collapse list-unstyled {{ in_array($activePage, ['user', 'role', 'permission']) ? 'show' : '' }}"
                    id="general">

                    {{-- @can('configuration-list')
                        <li class="nav-item @if ($activePage == 'configuration-list') active @endif">
                            <a class="nav-link" href="{{ route('configurations.index') }}">
                                <i class="fas fa-cog"></i>
                                {{ __('Configuration') }}
                            </a>
                        </li>
                    @endcan --}}

                    {{-- @can('user-list') --}}
                    <li class="nav-item @if ($activePage == 'user') active @endif">
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <i class="fas fa-user"></i>
                            {{ __('User') }}
                        </a>
                    </li>
                    {{-- @endcan --}}

                    {{-- @can('role-list') --}}
                    <li class="nav-item @if ($activePage == 'role') active @endif">
                        <a class="nav-link" href="{{ route('roles.index') }}">
                            <i class="fas fa-user-tag"></i>
                            {{ __('Roles') }}
                        </a>
                    </li>
                    {{-- @endcan --}}
                    {{-- @can('permission-list') --}}
                    <li class="nav-item @if ($activePage == 'permission') active @endif">
                        <a class="nav-link" href="{{ route('permissions.index') }}">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ __('Permission') }}
                        </a>
                    </li>
                    {{-- @endcan --}}



                    {{-- @can('company-list')
                        <li class="nav-item @if ($activePage == 'company') active @endif">
                            <a class="nav-link" href="{{ route('companies.index') }}">
                                <i class="fa fa-building" aria-hidden="true"></i>
                                {{ __('Company') }}
                            </a>
                        </li>
                    @endcan

                    @can('branch-list')
                        <li class="nav-item @if ($activePage == 'branch') active @endif">
                            <a class="nav-link" href="{{ route('branch.index') }}">
                                <i class="fa fa-building" aria-hidden="true"></i>
                                {{ __('Branch') }}
                            </a>
                        </li>
                    @endcan --}}



                    {{-- @can('principle-list')
                        <li class="nav-item @if ($activePage == 'principle') active @endif">
                            <a class="nav-link" href="{{ route('principles.index') }}">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                {{ __('Principle') }}
                            </a>
                        </li>
                    @endcan --}}

                </ul>
            </li>
            {{-- @endcan --}}
            {{-- @endcanany --}}

            {{-- @can('export-data') --}}
            <li class="nav-item @if ($activePage == 'export-data') active @endif">
                <a class="nav-link" href="{{ route('export-data.index') }}">
                    <i class="fa-solid fa-receipt"></i>
                    {{ __('Export Data Analysis') }}
                </a>
            </li>
            {{-- @endcan --}}


            {{-- @can('Vessel-data') --}}
            <li class="nav-item @if ($activePage == 'vessel') active @endif">
                <a class="nav-link" href="{{ route('vessels.index') }}">
                    <i class="fa fa-ship" aria-hidden="true"></i>

                    {{ __('Vessel') }}
                </a>
            </li>
            {{-- @endcan --}}

            {{-- @can('Vessel-data') --}}
            <li class="nav-item @if ($activePage == 'mlo') active @endif">
                <a class="nav-link" href="{{ route('mlos.create') }}">
                    <i class="fa-solid fa-receipt"></i>
                    {{ __('MLO') }}
                </a>
            </li>
            {{-- @endcan --}}

            <li class="">
                <a href="#upload" data-toggle="collapse"
                    aria-expanded="{{ in_array($activePage, ['mloWise','vesselWise']) ? 'true' : 'false' }}"
                    class="dropdown-toggle">
                    <i class="fas fa-bars"></i>
                    Upload</a>
                <ul class="collapse list-unstyled {{ in_array($activePage, ['mloWise','vesselWise']) ? 'show' : '' }}"
                    id="upload">

                    <li class="nav-item @if ($activePage == 'mloWise') active @endif">
                        <a class="nav-link" href="{{ route('mloWise.upload') }}">
                            <i class="fas fa-user"></i>
                            {{ __('MLO') }}
                        </a>
                    </li>

                    <li class="nav-item @if ($activePage == 'vesselWise') active @endif">
                        <a class="nav-link" href="{{ route('vesselWise.index') }}">
                            <i class="fas fa-user"></i>
                            {{ __('Vessel-Op') }}
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>


<!-- Page Content  -->
