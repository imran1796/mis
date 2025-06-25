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

            {{-- @can('general-section') --}}
            @canany(['user-list', 'role-list', 'permission-list'])
                <li class="">
                    <a href="#general" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['user', 'role', 'permission']) ? 'true' : 'false' }}"
                        class="dropdown-toggle">
                        <i class="fas fa-bars"></i>
                        General</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['user', 'role', 'permission']) ? 'show' : '' }}"
                        id="general">

                        @can('user-list')
                            <li class="nav-item @if ($activePage == 'user') active @endif">
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('User') }}
                                </a>
                            </li>
                        @endcan

                        @can('role-list')
                            <li class="nav-item @if ($activePage == 'role') active @endif">
                                <a class="nav-link" href="{{ route('roles.index') }}">
                                    <i class="fas fa-user-tag"></i>
                                    {{ __('Roles') }}
                                </a>
                            </li>
                        @endcan
                        @can('permission-list')
                            <li class="nav-item @if ($activePage == 'permission') active @endif">
                                <a class="nav-link" href="{{ route('permissions.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('Permission') }}
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcanany
            {{-- @endcan --}}

            @canany(['exportData-list', 'exportData-create'])
                <li class="">
                    <a href="#export-data" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['export-data']) ? 'true' : 'false' }}"
                        class="dropdown-toggle">
                        <i class="fas fa-bars"></i>
                        Export Data</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['export-data']) ? 'show' : '' }}"
                        id="export-data">
                        @can('exportData-list')
                            <li class="nav-item @if ($activePage == 'export-data') active @endif">
                                <a class="nav-link" href="{{ route('export-data.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Export Data') }}
                                </a>
                            </li>
                        @endcan

                        @can('exportData-create')
                            <li class="nav-item @if ($activePage == 'export-data-create') active @endif">
                                <a class="nav-link" href="{{ route('export-data.create') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('Upload (xls)') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['mlo-list', 'mloData-list', 'mloData-create'])
                <li class="">
                    <a href="#mlo" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['mlo']) ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="fas fa-bars"></i>
                        MLO</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['mlo', 'mloWiseCount-index', 'mloWiseCount-create']) ? 'show' : '' }}"
                        id="mlo">

                        @can('mlo-list')
                            <li class="nav-item @if ($activePage == 'mlo') active @endif">
                                <a class="nav-link" href="{{ route('mlos.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('MLOs') }}
                                </a>
                            </li>
                        @endcan

                        @can('mloData-create')
                            <li class="nav-item @if ($activePage == 'mloWiseCount-create') active @endif">
                                <a class="nav-link" href="{{ route('mloWiseCount.create') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('Upload MLO Data(xls)') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['vessel-list', 'operatorData-list', 'operatorData-create'])
                <li class="">
                    <a href="#vessel" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['vessel', 'vesselInfo-create', 'vesselInfo-index']) ? 'true' : 'false' }}"
                        class="dropdown-toggle">
                        <i class="fas fa-bars"></i>
                        Vessel</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['vessel', 'vesselInfo-create', 'vesselInfo-index']) ? 'show' : '' }}"
                        id="vessel">

                        @can('vessel-list')
                            <li class="nav-item @if ($activePage == 'vessel') active @endif">
                                <a class="nav-link" href="{{ route('vessels.index') }}">
                                    <i class="fa fa-ship" aria-hidden="true"></i>
                                    {{ __('Vessel Info') }}
                                </a>
                            </li>
                        @endcan

                        @can('operatorData-create')
                            <li class="nav-item @if ($activePage == 'vesselInfo-create') active @endif">
                                <a class="nav-link" href="{{ route('vesselInfo.create') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('Upload OPT. Data(xls)') }}
                                </a>
                            </li>
                        @endcan

                        {{-- @can('operatorData-list')
                            <li class="nav-item @if ($activePage == 'vesselInfo-index') active @endif">
                                <a class="nav-link" href="{{ route('vesselInfo.index') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('VLS-OPT Wise Data') }}
                                </a>
                            </li>
                        @endcan --}}
                    </ul>
                </li>
            @endcanany

            @canany(['turnAround-list', 'turnAround-create'])
                <li class="">
                    <a href="#vesselTurnAround" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['vesselTurnAround-create', 'vesselTurnAround-index']) ? 'true' : 'false' }}"
                        class="dropdown-toggle">
                        <i class="fas fa-bars"></i>
                        VSL Turn Around</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['vesselTurnAround-create', 'vesselTurnAround-index']) ? 'show' : '' }}"
                        id="vesselTurnAround">

                        @can('turnAround-list')
                            <li class="nav-item @if ($activePage == 'vesselTurnAround-index') active @endif">
                                <a class="nav-link" href="{{ route('vesselTurnAround.index') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('Vessel TA Data') }}
                                </a>
                            </li>
                        @endcan

                        @can('turnAround-create')
                            <li class="nav-item @if ($activePage == 'vesselTurnAround-create') active @endif">
                                <a class="nav-link" href="{{ route('vesselTurnAround.create') }}">
                                    <i class="fas fa-user"></i>
                                    {{ __('Upload TA Data(xls)') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['reports'])
                <li class="nav-item @if ($activePage == 'reports') active @endif">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-user"></i>
                        {{ __('Reports') }}
                    </a>
                </li>
            @endcanany


        </ul>
    </div>
</nav>
