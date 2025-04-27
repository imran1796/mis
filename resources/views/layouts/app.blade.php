<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--  --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>{{ $title }}</title>

    <!--  Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('light-bootstrap/css/jquery-ui.min.css') }}" rel="stylesheet" />

    {{--  --}}
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('light-bootstrap/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('light-bootstrap/img/favicon.ico') }}">
    {{--  --}}

    {{-- time picker --}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">



    <!-- CSS Files -->
    <link href="{{ asset('light-bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/light-bootstrap-dashboard.css?v=2.0.0') }} " rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('light-bootstrap/css/select2.min.css') }}" rel="stylesheet" />

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body>
    <div id="app">
        <div class="wrapper d-flex align-items-stretch">
            @if (auth()->check() && request()->route()->getName() != '')
                @include('layouts.navbars.sidebar-2')
            @endif

            <div id="content" @if (!auth()->check()) style="margin-left: 0px" @endif
                class="@if (auth()->check() && request()->route()->getName() != '') content @endif">
                @include('layouts.navbars.navbar')

                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="loading-icon">
                        <i class="fa fa-spinner fa-pulse"></i>
                    </div>
                </div>

                @if (session('tableData.notice'))
                    <marquee class="font-weight-bold  text-danger font-italic" width="100%" direction="left">
                        <span style="font-size: 20px">
                            {{ session('tableData.notice') }}

                        </span>
                    </marquee>
                @endif

                {{-- main content --}}
                @yield('content')

                {{-- footer --}}
                @include('layouts.footer.nav')
            </div>

        </div>
    </div>
</body>

<!--   Core JS Files   -->
<script src="{{ asset('light-bootstrap/js/core/jquery.3.2.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('light-bootstrap/js/core/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('light-bootstrap/js/core/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('light-bootstrap/js/core/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('light-bootstrap/js/plugins/jquery.sharrre.js') }}"></script>

{{-- mulitple select --}}
{{-- <script src="{{ asset('light-bootstrap/js/multiselect-dropdown.js')}}" ></script> --}}

{{-- time picker --}}
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{ asset('light-bootstrap/js/plugins/bootstrap-switch.js') }}"></script>

<!--  Google Maps Plugin    -->
{{-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> --}}

<!--  Chartist Plugin  -->
<script src="{{ asset('light-bootstrap/js/plugins/chartist.min.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/plugins/chartist-plugin-pointlabels.js') }}"></script>
{{-- <script src="{{ asset('light-bootstrap/js/plugins/chartist-plugin-barlabels.min.js') }}"></script> --}}
<script src="{{ asset('light-bootstrap/js/plugins/chartist-plugin-barlabels2.js') }}"></script>
<!--  Notifications Plugin    -->
<script src="{{ asset('light-bootstrap/js/plugins/bootstrap-notify.js') }}"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="{{ asset('light-bootstrap/js/light-bootstrap-dashboard.js?v=2.0.0') }}" type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="{{ asset('light-bootstrap/js/demo.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/date.min.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/select2.min.js') }}"></script>
{{-- <script src="{{ asset('light-bootstrap/js/jquery.datetimepicker.js') }}"></script> --}}
<script src="{{ asset('light-bootstrap/js/main.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/bootstrap-select.min.js') }}"></script>

<script src="{{ asset('light-bootstrap/js/exceljs.min.js') }}"></script>
<script src="{{ asset('light-bootstrap/js/excelJsFinal.js') }}"></script>



@stack('js')

</html>
