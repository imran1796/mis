<nav id="sidebar">
    <div class="p-4">
        <div class="col-md-12   " style="width: 20%;">
            {{--
                    <img style="width:100%; height: auto" src="{{ public_path().'\storage\41.png' }}">
--}}
            <img style="width:180px; height: 100px" src="{{ asset('storage/4.png') }}">
        </div>
        <ul class="list-unstyled components mb-5">

            <li class="nav-item @if ($activePage == 'dashboard') active @endif">

                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    {{ __('Dashboard') }}
                </a>
            </li>

            @can('general-section')
            <li class="">
                <a href="#general" data-toggle="collapse"
                    aria-expanded="{{ in_array($activePage, ['user', 'role', 'permission', 'vendor', 'depot', 'depot-tariff-create', 'depot-tariff', 'commodity', 'usd-log', 'company', 'branch', 'port-lists', 'carrier-list', 'principal', 'det-charge', 'local-charge']) ? 'true' : 'false' }}"
                    class="dropdown-toggle">
                    <i class="fas fa-bars"></i>
                    General</a>
                <ul class="collapse list-unstyled {{ in_array($activePage, ['user', 'role', 'permission', 'vendor', 'depot', 'depot-tariff-create', 'depot-tariff', 'commodity', 'usd-log', 'company', 'branch', 'port-lists', 'carrier-list', 'principal', 'det-charge', 'local-charge']) ? 'show' : '' }}"
                    id="general">

                    @can('configuration-list')
                        <li class="nav-item @if ($activePage == 'configuration') active @endif">
                            <a class="nav-link" href="{{ route('configurations.index') }}">
                                <i class="fas fa-cog"></i>
                                {{ __('Configuration') }}
                            </a>
                        </li>
                    @endcan

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

                    @can('vendor-list')
                        <li class="nav-item @if ($activePage == 'vendor') active @endif">
                            <a class="nav-link" href="{{ route('vendors.index') }}">
                                <i class="fas fa-id-card-alt"></i>
                                {{ __('Vendor') }}
                            </a>
                        </li>
                    @endcan
                    @can('depot-list')
                        <li class="nav-item
                            @if (in_array($activePage, ['depot', 'depot-tariff-create', 'depot-tariff'])) active @endif">
                            <a class="nav-link" href="{{ route('depot.index') }}">
                                <i class="fas fa-id-card-alt"></i>
                                {{ __('Depots') }}
                            </a>
                        </li>
                    @endcan

                    @can('commodity-list')
                        <li class="nav-item @if ($activePage == 'commodity') active @endif">
                            <a class="nav-link" href="{{ route('commodities.index') }}">
                                <i class="fas fa-id-card-alt"></i>
                                {{ __('Commodity') }}
                            </a>
                        </li>
                    @endcan

                    @can('usd-list')
                        <li class="nav-item @if ($activePage == 'usd-log') active @endif">
                            <a class="nav-link" href="{{ route('usd-logs.index') }}">
                                <i class="fa-solid fa-money-check-dollar"></i>
                                {{ __('USD Log') }}
                            </a>
                        </li>
                    @endcan

                    @can('company-list')
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
                    @endcan

                    @can('portList-list')
                        <li class="nav-item @if ($activePage == 'port-lists') active @endif">
                            <a class="nav-link" href="{{ route('port-lists.index') }}">
                                <i class="fa fa-building" aria-hidden="true"></i>
                                {{ __('Port List') }}
                            </a>
                        </li>
                    @endcan

                    @can('carrier-list')
                        <li class="nav-item @if ($activePage == 'carrier-list') active @endif">
                            <a class="nav-link" href="{{ route('carriers.index') }}">
                                <i class="fa fa-building" aria-hidden="true"></i>
                                {{ __('Carrier') }}
                            </a>
                        </li>
                    @endcan

                    @can('principle-list')
                        <li class="nav-item @if ($activePage == 'principle') active @endif">
                            <a class="nav-link" href="{{ route('principles.index') }}">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                {{ __('Principle') }}
                            </a>
                        </li>
                    @endcan

                    @can('charge-update')
                        <li class="nav-item @if ($activePage == 'det-charge') active @endif">
                            <a class="nav-link" href="{{ route('detention_charge.index') }}">
                                <i class="fas fa-id-card-alt"></i>
                                {{ __('Det. Charge') }}
                            </a>
                        </li>
                    @endcan

                    @can('local-charge-index')
                        <li class="nav-item @if ($activePage == 'local-charge') active @endif">
                            <a class="nav-link" href="{{ route('local_charge.index.2') }}">
                                <i class="fas fa-id-card-alt"></i>
                                {{ __('Local Charge') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
            @endcan

            @can('import-xls')
                <li class="">
                    <a href="#import" data-toggle="collapse"
                        aria-expanded="{{ in_array($activePage, ['import-file', 'voyage-delete', 'voyage-edit', 'delete-bl', 'import-bl', 'block-bl', 'remove-block-bl', 'reminder', 'import-report-tab', 'vessel-wise-import-lifting', 'vessel-wise-import-lifting', 'vessel-pol-wise-import-lifting', 'depot-bound-import-lifting', 'pol-wise-import-lifting', 'commodity-wise-import-lifting', 'dg-wise-import-lifting', 'igm-summary', 'bl-type-import-lifting', 'dhaka-pangoan', 'import-stock', 'report-by-reg', 'pangoan-ict-report', 'dhaka-icd-report']) ? 'true' : 'false' }}"
                        class="dropdown-toggle">
                        <i class="fas fa-file-import"></i>
                        Import Menu</a>
                    <ul class="collapse list-unstyled {{ in_array($activePage, ['import-file', 'voyage-delete', 'voyage-edit', 'delete-bl', 'import-bl', 'block-bl', 'remove-block-bl', 'reminder', 'import-report-tab', 'vessel-wise-import-lifting', 'vessel-wise-import-lifting', 'vessel-pol-wise-import-lifting', 'depot-bound-import-lifting', 'pol-wise-import-lifting', 'commodity-wise-import-lifting', 'dg-wise-import-lifting', 'igm-summary', 'bl-type-import-lifting', 'dhaka-pangoan', 'import-stock', 'report-by-reg', 'pangoan-ict-report', 'dhaka-icd-report']) ? 'show' : '' }}"
                        id="import">
                        <li class="@if ($activePage == 'import-file') active @endif">
                            <a class="nav-link" href="{{ route('import-view') }}">
                                <i class="fas fa-plus"></i> {{ __('Import XLS') }}
                            </a>
                        </li>
                        <li class="@if ($activePage == 'voyage-delete') active @endif">
                            <a class="nav-link" href="{{ route('delete-view') }}">
                                <i class="fas fa-minus"></i> {{ __('Delete XLS') }}
                            </a>
                        </li>
                        <li class="@if ($activePage == 'voyage-edit') active @endif">
                            <a class="nav-link" href="{{ route('import.edit') }}">
                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                            </a>
                        </li>
                        @can('delete-bl')
                            <li class="@if ($activePage == 'delete-bl') active @endif">
                                <a class="nav-link" href="{{ route('delete-bl') }}">
                                    <i class="fas fa-trash"></i> {{ __('Delete BL') }}
                                </a>
                            </li>
                        @endcan
                        @can('add-bl')
                            <li class="@if ($activePage == 'import-bl') active @endif">
                                <a class="nav-link" href="{{ route('import-bl-view') }}">
                                    <i class="fa-solid fa-folder-plus"></i> {{ __('Add BL') }}
                                </a>
                            </li>
                        @endcan


                        @can('block-bl')
                            <li class="@if ($activePage == 'block-bl') active @endif">
                                <a class="nav-link" href="{{ route('block_bl') }}">
                                    <i class="fas fa-trash"></i> {{ __('Block BL') }}
                                </a>
                            </li>
                            <li class="@if ($activePage == 'remove-block-bl') active @endif">
                                <a class="nav-link" href="{{ route('remove_block_bl') }}">
                                    <i class="fas fa-trash"></i> {{ __('Remove Block BL') }}
                                </a>
                            </li>
                        @endcan

                        @can('import-report')
                            <li
                                class="nav-item
                            {{ in_array($activePage, ['import-report-tab', 'vessel-wise-import-lifting', 'vessel-pol-wise-import-lifting', 'depot-bound-import-lifting', 'pol-wise-import-lifting', 'commodity-wise-import-lifting', 'dg-wise-import-lifting', 'igm-summary', 'bl-type-import-lifting', 'dhaka-pangoan', 'import-stock', 'report-by-reg', 'pangoan-ict-report', 'dhaka-icd-report']) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('report.import_index') }}">
                                    <i class="fas fa-id-card-alt"></i>
                                    {{ __('Import Report') }}
                                </a>
                            </li>
                        @endcan

                        @can('reminder')
                            <li class="nav-item @if ($activePage == 'reminder') active @endif">
                                <a class="nav-link" href="{{ route('reminder') }}">
                                    <i class="fa-solid fa-bell"></i>
                                    {{ __('Reminder') }}
                                </a>
                            </li>
                        @endcan

                        @can('icdReefer-permission')
                            <li class="nav-item @if ($activePage == 'import-permission-list') active @endif">
                                <a class="nav-link" href="{{ route('import.permission-list') }}">
                                    <i class="fa-solid fa-bell"></i>
                                    {{ __('ICD Reefer Permission') }}
                                </a>
                            </li>
                        @endcan

                        @can('bolPreCharge-list')
                        <li class="nav-item @if ($activePage == 'bol_pre_charge') active @endif">
                            <a class="nav-link" href="{{ route('bol_pre_charge.index') }}">
                                <i class="fa-solid fa-bell"></i>
                                {{ __('Bl Pre Charge') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcan


            @can('mr-create')
                <li class="nav-item @if ($activePage == 'mr') active @endif">
                    <a class="nav-link" href="{{ route('mrs.create') }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        {{ __('Money Receipt') }}
                    </a>
                </li>
            @endcan
            @can('deposit-list')
                <li class="nav-item @if ($activePage == 'deposit') active @endif">
                    <a class="nav-link" href="{{ route('deposits.index') }}">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        {{ __('Deposit') }}
                    </a>
                </li>
            @endcan
            @can('refund-list')
                <li class="nav-item @if ($activePage == 'refund') active @endif">
                    <a class="nav-link" href="{{ route('refunds.index') }}">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        {{ __('Refund') }}
                    </a>
                </li>
            @endcan

            {{-- @canany(['mr-list', 'do-list', 'vesselInfo-list'])
                <li>
                    <a href="#reporting" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Report</a>
                    <ul class="collapse list-unstyled" id="reporting">
                        @can('mr-list')
                            <li class="nav-item @if ($activePage == 'mr-list') active @endif">
                                <a class="nav-liznk" href="{{ route('mrs.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('MR Report/Extend') }}
                                </a>
                            </li>
                            <li class="nav-item @if ($activePage == 'mr-detention') active @endif">
                                <a class="nav-link" href="{{ route('report.detention_report') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Detention Report') }}
                                </a>
                            </li>
                        @endcan
                        @can('do-list')
                            <li class="nav-item @if ($activePage == 'do') active @endif">
                                <a class="nav-link" href="{{ route('dos.index') }}">
                                    <i class="fa-solid fa-truck"></i>
                                    {{ __('Delivery Order') }}
                                </a>
                            </li>
                            <li class="nav-item @if ($activePage == 'fcl-report') active @endif">
                                <a class="nav-link" href="{{ route('dos.fcl-do-report') }}">
                                    <i class="fa-solid fa-box-open"></i>
                                    {{ __('FCL DO Report') }}
                                </a>
                            </li>
                            <li class="nav-item @if ($activePage == 'mushok-report') active @endif">
                                <a class="nav-link" href="{{ route('report.mushok_report') }}">
                                    <i class="fa-solid fa-m"></i>
                                    {{ __('Mushok Report') }}
                                </a>
                            </li>
                        @endcan

                        @can('vesselInfo-list')
                            <li class="nav-item @if ($activePage == 'vessel-info') active @endif">
                                <a class="nav-link" href="{{ route('vessel-info') }}">
                                    <i class="fa-solid fa-ship"></i>
                                    {{ __('Import Vessel') }}
                                </a>
                            </li>
                        @endcan
                        @can('mr-list')
                            <li class="nav-item @if ($activePage == 'dhaka-icd') active @endif">
                                <a class="nav-link" href="{{ route('report.dhaka-icd-balance') }}">
                                    <i class="fa-solid fa-ship"></i>
                                    {{ __('Dhaka Icd In') }}
                                </a>
                            </li>

                            <li class="nav-item @if ($activePage == 'guarantee-unreleased') active @endif">
                                <a class="nav-link" href="{{ route('report.guarantee-unreleased') }}">
                                    <i class="fa-solid fa-ship"></i>
                                    {{ __('Guarantee Unreleased') }}
                                </a>
                            </li>
                        @endcan


                    </ul>
                </li>
            @endcanany --}}

            @canany(['mr-list', 'do-list', 'vesselInfo-list'])
                <li class="nav-item @if ($activePage == 'do-report-tab') active @endif">
                    <a class="nav-link" href="{{ route('dos.do-report') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('DO Report') }}
                    </a>
                </li>
            @endcanany

            @can('status-change')
                <li class="nav-item @if ($activePage == 'status-change') active @endif">
                    <a class="nav-link" href="{{ route('status_change.create') }}">
                        <i class="fa-solid fa-bell"></i>
                        {{ __('Status Change') }}
                    </a>
                </li>
            @endcan

            @can('seal-create')
                <li>
                    <a href="#seals" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Container Seals</a>
                    <ul class="collapse list-unstyled" id="seals">
                        <li class="nav-item @if ($activePage == 'seal-create') active @endif">
                            <a class="nav-link" href="{{ route('seals.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Create') }}
                            </a>
                        </li>

                        <li class="nav-item @if ($activePage == 'seal-create') active @endif">
                            <a class="nav-link" href="{{ route('seals.insert_depot') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Insert Depot') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'seal-transfer') active @endif">
                            <a class="nav-link" href="{{ route('seals.transfer') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Transfer') }}
                            </a>
                        </li>
                        @can('seal-edit')
                            <li class="nav-item @if ($activePage == 'seal-status-change') active @endif">
                            <a class="nav-link" href="{{ route('seals.status_change') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Status Change') }}
                            </a>
                        </li>
                        @endcan


                        <li class="nav-item @if ($activePage == 'seal-stock-report') active @endif">
                            <a class="nav-link" href="{{ route('report.seal_stock') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Stock') }}
                            </a>
                        </li>


                        <li class="nav-item @if ($activePage == 'seal-statement') active @endif">
                            <a class="nav-link" href="{{ route('seals.seal_statement') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Statement') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'seal-used-report') active @endif">
                            <a class="nav-link" href="{{ route('report.seal_used') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Used Report') }}
                            </a>
                        </li>

                        <li class="nav-item @if ($activePage == 'seal-use-other') active @endif">
                            <a class="nav-link" href="{{ route('seals.seal_use') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Seal Use Update') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            @can('movement-create')
                <li>
                    <a href="#movements" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Movements</a>
                    <ul class="collapse list-unstyled" id="movements">
                        <li class="nav-item @if ($activePage == 'devan') active @endif">
                            <a class="nav-link" href="{{ route('movement.devanning') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Devaning Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'import-out') active @endif">
                            <a class="nav-link" href="{{ route('movement.import_out') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Import Out Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'empty-in') active @endif">
                            <a class="nav-link" href="{{ route('movement.empty_in') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Empty In Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'dumping') active @endif">
                            <a class="nav-link" href="{{ route('movement.dumping') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Dumping Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'stuffing') active @endif">
                            <a class="nav-link" href="{{ route('movement.stuffing_create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Stuffing Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'unstuffing') active @endif">
                            <a class="nav-link" href="{{ route('movement.unstuffing_create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('UnStuffing Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'export-out') active @endif">
                            <a class="nav-link" href="{{ route('movement.export_out') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Export Out Update') }}
                            </a>
                        </li>

                        <li class="nav-item @if ($activePage == 'rob') active @endif">
                            <a class="nav-link" href="{{ route('rob-update.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('ROB Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'di_in') active @endif">
                            <a class="nav-link" href="{{ route('di_in.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('DI In Update') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'di_out') active @endif">
                            <a class="nav-link" href="{{ route('di_out.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('DI Out Update') }}
                            </a>
                        </li>
                    </ul>
                </li>

                
                <li>
                    <a href="#eq-reports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Equipment Verify</a>
                    <ul class="collapse list-unstyled" id="eq-reports">
                        <li class="nav-item @if ($activePage == 'movement-history') active @endif">
                            <a class="nav-link" href="{{ route('movement.history') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Container History') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'container-verification') active @endif">
                            <a class="nav-link" href="{{ route('movement.verify') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Container Verification') }}
                            </a>
                        </li>
                        <!--

                                                <li class="nav-item @if ($activePage == 'stock-summary-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.stock-summary') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Stock Summary Report') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'stock-detail-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.stock-detail') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Stock Detail Report') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'stuffing-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.stuffing') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Stuffing Report') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'stuffing-summary') active @endif">
                                                    <a class="nav-link" href="{{ route('report.stuffing-summary') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Stuffing Summary Report') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'empty-in-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.empty-in') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Empty In Report') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'Import-out-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.import-out') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Import Out Report') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'devanning-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.devanning') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Devanning Report') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'devanning-summary-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.devanning-summary') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Devanning Summary Report') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'dumping-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.dumped') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Dumping Report') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'laden-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.laden-out') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Export Laden Out') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'empty-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.empty-out') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Export Empty Out') }}
                                                    </a>
                                                </li>
                -->


                    </ul>
                </li>




                <li>
                    <a href="#ex-vessel" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Export Vessel</a>
                    <ul class="collapse list-unstyled" id="ex-vessel">

                        <li class="nav-item @if ($activePage == 'create-vessel') active @endif">
                            <a class="nav-link" href="{{ route('vessel.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Create Vessel') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'export-vessel') active @endif">
                            <a class="nav-link" href="{{ route('export-vessel.create') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Create Export Vessel') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'export-container') active @endif">
                            <a class="nav-link" href="{{ route('movement.export_container') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Update Export Vessel') }}
                            </a>
                        </li>

                    </ul>
                </li>

                <li>
                    <a href="#permission" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Vessel Permission</a>
                    <ul class="collapse list-unstyled" id="permission">
                        <li class="nav-item @if ($activePage == 'create-vessel') active @endif">
                            <a class="nav-link" href="{{ route('export.laden_permission') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Laden Permission') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'export-vessel') active @endif">
                            <a class="nav-link" href="{{ route('export.empty_permission') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Empty Permission') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'copino') active @endif">
                            <a class="nav-link" href="{{ route('export.copino') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Copino') }}
                            </a>
                        </li>
                        <li class="nav-item @if ($activePage == 'copran') active @endif">
                            <a class="nav-link" href="{{ route('export.coparn') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Coparn') }}
                            </a>
                        </li>

                        <li class="nav-item @if ($activePage == 'landen-permission-list') active @endif">
                            <a class="nav-link" href="{{ route('export.laden_permission_list') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Laden Permission List') }}
                            </a>
                        </li>


                    </ul>
                </li>




                <!--                        <li>
                                            <a href="#new" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                                <i class="fa-solid fa-bug"></i>
                                                New Report</a>
                                            <ul class="collapse list-unstyled" id="new">
                                                <li class="nav-item @if ($activePage == 'copran') active @endif">
                                                    <a class="nav-link" href="{{ route('report.empty_removal') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('MTY Removal') }}
                                                    </a>
                                                </li>



                                                <li class="nav-item @if ($activePage == 'on-chasis-not-empty-report') active @endif">
                                                    <a class="nav-link" href="{{ route('report.on_chasis_not_empty') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('On Chasis Not Empty') }}

                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'on-chasis-due') active @endif">
                                                    <a class="nav-link" href="{{ route('report.on_chasis_due') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('On Chasis Due') }}

                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'stuffing-dwell-time') active @endif">
                                                    <a class="nav-link" href="{{ route('report.stuffing_dwell_time') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Stuffing Dwell Time') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'depot-stay-empty') active @endif">
                                                    <a class="nav-link" href="{{ route('report.depot_stay_empty') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('MTY DWELL TIME AT DEPOT') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'depot-stay') active @endif">
                                                    <a class="nav-link" href="{{ route('report.depot_stay') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Depot Stay Total') }}
                                                    </a>
                                                </li>


                                                <li class="nav-item @if ($activePage == 'empty-dwell-time') active @endif">
                                                    <a class="nav-link" href="{{ route('report.empty_dwell_time') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Empty Dwell Time') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'laden-dwell-time') active @endif">
                                                    <a class="nav-link" href="{{ route('report.laden_dwell_time') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Laden Dwell Time') }}
                                                    </a>
                                                </li>
                                                <li class="nav-item @if ($activePage == 'import-laden-dwell-time') active @endif">
                                                    <a class="nav-link" href="{{ route('report.import_laden_dwell_time') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Import Laden Dwell Time') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'port-to-depot') active @endif">
                                                    <a class="nav-link" href="{{ route('report.port_to_depot') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Port to Depot') }}
                                                    </a>
                                                </li>

                                                <li class="nav-item @if ($activePage == 'empty-dwell-time-port') active @endif">
                                                    <a class="nav-link" href="{{ route('report.empty_dwell_time_port') }}">
                                                        <i class="fa-solid fa-receipt"></i>
                                                        {{ __('Empty Dwell Time Port') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>-->

                <li>
                    <a href="#bills" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Bills</a>
                    <ul class="collapse list-unstyled" id="bills">
                        <li class="nav-item @if ($activePage == 'depot-bill') active @endif">
                            <a class="nav-link" href="{{ route('bills.depot_bill') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Depot Bill') }}
                            </a>
                        </li>


                    </ul>
                </li>
                
            @endcan
            @can('equipment-report')
                <li class="nav-item @if ($activePage == 'equipment-report-tab') active @endif">
                    <a class="nav-link" href="{{ route('index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Equipment Report') }}
                    </a>
                </li>
                <li class="nav-item @if ($activePage == 'new-report-tab') active @endif">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Additional Eqc. Report') }}
                    </a>
                </li>
            @endcan

            @can('export-report')
                <li class="nav-item @if ($activePage == 'export-report-tab') active @endif">
                    <a class="nav-link" href="{{ route('export.report_index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Ex. Lifting Report') }}
                    </a>
                </li>
            @endcan

            @canany(['exportCharge-list', 'customer-list', 'invoiceType-list','note-list','export-data'])
                <li>
                    <a href="#export_setting" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Export Setting</a>
                    <ul class="collapse list-unstyled" id="export_setting">
                        @can('exportCharge-list')
                            <li class="nav-item @if ($activePage == 'export-charge') active @endif">
                                <a class="nav-link" href="{{ route('export-charges.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Export Charge') }}
                                </a>
                            </li>
                        @endcan
                        @can('customer-list')
                            <li class="nav-item @if ($activePage == 'customer') active @endif">
                                <a class="nav-link" href="{{ route('customers.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Customer') }}
                                </a>
                            </li>
                        @endcan
                        @can('invoiceType-list')
                            <li class="nav-item @if ($activePage == 'invoice-type') active @endif">
                                <a class="nav-link" href="{{ route('invoice-types.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Invoice Type') }}
                                </a>
                            </li>
                        @endcan
                        @can('note-list')
                            <li class="nav-item @if ($activePage == 'note') active @endif">
                                <a class="nav-link" href="{{ route('notes.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Notes') }}
                                </a>
                            </li>
                        @endcan
                        @can('export-data')
                            <li class="nav-item @if ($activePage == 'export-data') active @endif">
                                <a class="nav-link" href="{{ route('export-data.index') }}">
                                    <i class="fa-solid fa-receipt"></i>
                                    {{ __('Export Data Analysis') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('invoice-list')
                <li class="nav-item @if ($activePage == 'invoice') active @endif">
                    <a class="nav-link" href="{{ route('invoices.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Invoice') }}
                    </a>
                </li>
            @endcan
            @can('exportMr-create')
                <li class="nav-item @if ($activePage == 'export-mrs') active @endif">
                    <a class="nav-link" href="{{ route('export-mrs.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Collection') }}
                    </a>
                </li>
            @endcan

            @can('preShipment-create')
                <li class="nav-item @if ($activePage == 'preshipment-advice') active @endif">
                    <a class="nav-link" href="{{ route('preshipment-advices.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Preshipment Advice') }}
                    </a>
                </li>
            @endcan

            @can('customerVisit-create')
                <li class="nav-item @if ($activePage == 'customer-visit') active @endif">
                    <a class="nav-link" href="{{ route('customer-visits.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Customer Visit') }}
                    </a>
                </li>
            @endcan

            @canany(['mushok','mushok-reserve'])
                <li>
                    <a href="#mushok" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa-solid fa-bug"></i>
                        Mushok</a>
                    <ul class="collapse list-unstyled" id="mushok">

                        @can('mushok')
                        <li class="nav-item @if ($activePage == 'mushok-all-report') active @endif">
                            <a class="nav-link" href="{{ route('mushoks.index') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Mushok List') }}
                            </a>
                        </li>

                        <li class="nav-item @if ($activePage == 'mushok66-index') active @endif">
                            <a class="nav-link" href="{{ route('mushoks.mushok-6-6.index') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Mushok 6.6') }}
                            </a>
                        </li>
                        @endcan

                    
            

                        @can('mushok-reserve')
                        <li class="nav-item @if ($activePage == 'mushok-reserve') active @endif">
                            <a class="nav-link" href="{{ route('mushoks.reserve_index') }}">
                                <i class="fa-solid fa-receipt"></i>
                                {{ __('Mushok Reserve') }}
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('salesReport-list')
                <li class="nav-item @if ($activePage == 'sales-report') active @endif">
                    <a class="nav-link" href="{{ route('sales-report.index') }}">
                        <i class="fas fa-id-card-alt"></i>
                        {{ __('Sales Report') }}
                    </a>
                </li>
            @endcan

        </ul>
    </div>
</nav>

<!-- Page Content  -->
