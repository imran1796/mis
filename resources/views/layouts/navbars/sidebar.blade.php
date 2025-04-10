<div class="sidebar"  data-image="{{ asset('light-bootstrap/img/sidebar-2.jpg') }}">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

Tip 2: you can also add an image using data-image tag
-->

    <div class="sidebar-wrapper">
        <div class="logo row">
            <a  href="" class="simple-text col-md-10 col-sm-10">
                {{ __("GLA Admin") }}
            </a>

            <a href="javascript:void(0)" class="closebtn col-md-1 col-sm-1 mt-1"  style="color: black; font-size: 20px" onclick="closeNav()">X</a>

        </div>

        <ul class="nav">
            <li class="nav-item @if($activePage == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="nc-icon nc-chart-pie-35"></i>
                    <p>{{ __("Dashboard") }}</p>
                </a>
            </li>
            @can('import-xls')

                <li class="nav-item @if($activePage == 'import-file') active @endif">

                <a class="nav-link" data-toggle="collapse" href="#laravelExamples" aria-expanded="true">
                    <i>
                        <img src="https://light-bootstrap-dashboard-laravel.creative-tim.com/light-bootstrap/img/laravel.svg" style="width:25px">
                    </i>
                    <p>
                        Import/Delete

                        <b class="caret"></b>
                    </p>
                    <i style="font-size: 14px;vertical-align: center" class="mt-2 fas fa-solid fa-plus fa-sm float-right"></i>

                </a>
                <div class="collapse show" id="laravelExamples" style="">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('import-view')}}">
                                <i class="nc-icon nc-chart-pie-35"></i>
                                <p>{{ __("Import XLS") }}</p>
                            </a>
                        </li>
                        <li class="nav-item " >
                            <a class="nav-link" href="{{route('delete-view')}}">
                                <i class="nc-icon nc-chart-pie-35"></i>
                                <p>{{ __("Delete Voyage") }}</p>
                            </a>
                        </li>
                        <li class="nav-item " >
                            <a class="nav-link" href="{{route('import.edit')}}">
                                <i class="nc-icon nc-chart-pie-35"></i>
                                <p>{{ __("Edit Voyage") }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endcan

        @auth
               {{-- @role('Admin')
                <li><a href="{{ route('users.index') }}" class="nav-link px-2 text-white">Users</a></li>
                <li><a href="{{ route('roles.index') }}" class="nav-link px-2 text-white">Roles</a></li>
                @endrole--}}
                @can('user-list')
                    <li class="nav-item @if($activePage == 'user') active @endif">
                        <a class="nav-link" href="{{route('users.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("User") }}</p>
                        </a>
                    </li>
                @endcan
                @can('role-list')
                    <li class="nav-item @if($activePage == 'role') active @endif">
                        <a class="nav-link" href="{{route('roles.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Roles") }}</p>
                        </a>
                    </li>
                @endcan
                @can('permission-list')
                    <li class="nav-item @if($activePage == 'permission') active @endif">
                        <a class="nav-link" href="{{route('permissions.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Permission") }}</p>
                        </a>
                    </li>
                @endcan
                @can('vendor-list')
                    <li class="nav-item @if($activePage == 'vendor') active @endif">
                        <a class="nav-link" href="{{route('vendors.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Vendor") }}</p>
                        </a>
                    </li>
                @endcan

                @can('usd-list')
                    <li class="nav-item @if($activePage == 'usd-log') active @endif">
                        <a class="nav-link" href="{{route('usd-logs.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("USD Log") }}</p>
                        </a>
                    </li>
                @endcan


                @can('company-list')
                    <li class="nav-item @if($activePage == 'company') active @endif">
                        <a class="nav-link" href="{{route('companies.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Company") }}</p>
                        </a>
                    </li>
                @endcan

                @can('principle-list')
                    <li class="nav-item @if($activePage == 'principle') active @endif">
                        <a class="nav-link" href="{{route('principles.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Principle") }}</p>
                        </a>
                    </li>
                @endcan

                @can('mr-create')
                    <li class="nav-item @if($activePage == 'mr') active @endif">
                        <a class="nav-link" href="{{route('mrs.create')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Money Receipt") }}</p>
                        </a>
                    </li>
                @endcan
                @can('mr-create')
                    <li class="nav-item @if($activePage == 'mr') active @endif">
                        <a class="nav-link" href="{{route('mrs.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Extend MR") }}</p>
                        </a>
                    </li>
                @endcan
                @can('do-list')
                    <li class="nav-item @if($activePage == 'do') active @endif">
                        <a class="nav-link" href="{{route('dos.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Delivery Order") }}</p>
                        </a>
                    </li>
                @endcan

                @can('deposit-list')
                    <li class="nav-item @if($activePage == 'deposit') active @endif">
                        <a class="nav-link" href="{{route('deposits.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Deposit") }}</p>
                        </a>
                    </li>
                @endcan
                @can('refund-list')
                    <li class="nav-item @if($activePage == 'refund') active @endif">
                        <a class="nav-link" href="{{route('refunds.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Refund") }}</p>
                        </a>
                    </li>
                @endcan

                @can('import-stock')
                    <li class="nav-item @if($activePage == 'import-stock') active @endif">
                        <a class="nav-link" href="{{route('report.import-stock')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Import Stocks") }}</p>
                        </a>
                    </li>
                    <li class="nav-item @if($activePage == 'report-by-reg') active @endif">
                        <a class="nav-link" href="{{route('report.by-reg')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Report By Reg") }}</p>
                        </a>
                    </li>
                    <li class="nav-item @if($activePage == 'mrs') active @endif">
                        <a class="nav-link" href="{{route('mrs.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("MR Report") }}</p>
                        </a>
                    </li>
                    <li class="nav-item @if($activePage == 'dos') active @endif">
                        <a class="nav-link" href="{{route('dos.index')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("DO Report") }}</p>
                        </a>
                    </li>
                    <li class="nav-item @if($activePage == 'fcl-report') active @endif">
                        <a class="nav-link" href="{{route('dos.fcl-do-report')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("FCL Report") }}</p>
                        </a>
                    </li>
                    <li class="nav-item @if($activePage == 'mushok-report') active @endif">
                        <a class="nav-link" href="{{route('report.mushok_report')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Mushok Report") }}</p>
                        </a>
                    </li>
                @endcan
                @can('reminder')
                    <li class="nav-item @if($activePage == 'reminder') active @endif">
                        <a class="nav-link" href="{{route('reminder')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Reminder") }}</p>
                        </a>
                    </li>
                @endcan


                @can('vesselInfo-list')
                    <li class="nav-item @if($activePage == 'vessel-info') active @endif">
                        <a class="nav-link" href="{{route('vessel-info')}}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>{{ __("Import Vessel") }}</p>
                        </a>
                    </li>
                @endcan

                <li class="nav-item @if($activePage == 'product') active @endif">
                    <a class="nav-link" href="{{route('products.index')}}">
                        <i class="nc-icon nc-chart-pie-35"></i>
                        <p>{{ __("Demo Product") }}</p>
                    </a>
                </li>


            @endauth

        </ul>
    </div>
</div>
