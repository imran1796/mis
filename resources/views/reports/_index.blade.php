@extends('layouts.app', ['activePage' => 'reports', 'title' => 'GLA Admin', 'navName' => 'Reports', 'activeButton' => 'laravel'])

@section('content')
    <style>
        .selectpicker {
            background-color: saddlebrown !important;
            color: black;
        }

        .icon-medium {
            font-size: 2em;
        }

        .numbers p {
            color: black !important;
        }

        .card-stats {
            background: aliceblue;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row">

                    <div class="col-lg-12 margin-tb">

                        <div class="pull-left">


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
                        <div class="row">

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category"> Vessel & OPT  wise Container handling</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.vessel-operator-wise-lifting') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category"> Operator Wise Container Lifting (Summary)</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.operator-wise-lifting') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category"> MLO Wise Container Handling</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.operator-wise-lifting') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category"> SOC IN/OUT Bound Volume Data</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.soc-inout-bound') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category">Opt. Wise Vessel Turn Around</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.vessel-turn-around') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category">MLO NVOCC Summary</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.mlo-wise-summary') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category">SOC Outbound Market</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.soc-outbound-market') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category">Market Competitors</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.market-competitors') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-md-3">
                                <div class="card card-stats ">
                                    <div class="card-body ">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="icon-medium text-center icon-warning">
                                                    <i class="nc-icon nc-paper-2 text-success"></i>
                                                </div>
                                            </div>
                                            <div class="col-10 d-flex align-items-center ">
                                                <div class="numbers align-content-center">
                                                    <p class="card-category">Vessel-Operator Info Report</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-footer ">
                                        <hr>
                                        <div class="stats float-right">
                                            <a target="_blank" href="{{ route('reports.vesselInfo') }}"
                                                class="btn btn-sm btn-outline-success">View Report<i
                                                    class="fa fa-arrow-right "></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
                {{-- {!! $mrsmrs->links() !!} --}}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('light-bootstrap/js/jquery.table2excel.min.js') }}"></script>


    <script>
        $(function() {
            //   $('#datetimepicker1').datetimepicker();
        });
        $(document).ready(function() {

            $('#btnExcelJsExport').click(function() {
                var heading_name = $("#heading_name").text();
                $(".table2excel").table2excel({
                    exclude: ".noExl",
                    name: heading_name,
                    //filename: "myFileName" + new Date().toISOString().replace(/[\-\:\.]/g, ""),
                    filename: heading_name,
                    fileext: ".xls",
                    exclude_img: true,
                    exclude_links: true,
                    exclude_inputs: true,
                    excel: {
                        beforeSave: function() {
                            // set the row height for all rows in the sheet
                            var sheet = this.sheet;
                            sheet.rowHeight(0, sheet.rowCount(),
                                30); // set row height to 30 pixels
                        }
                    }

                });
            });
            $('.dropdown-menu:first').find('.bs-searchbox').before(
                '<div class="bs-actionsbox"><div class="btn-group btn-group-sm btn-block"><button type="button" class="actions-btn bs-select-all btn-secondary">Select All</button><button type="button" class="actions-btn bs-deselect-all btn-secondary">Deselect All</button></div></div>'
            )
            $('.selectpicker').selectpicker({
                actionsBox: true,
                deselectAllText: 'Deselect All',
                selectAllText: 'Select All',
                countSelectedText: function(e, t) {
                    return 1 == e ? "{0} item selected" : "{0} items selected"
                },
                selectedTextFormat: 'count'
            });

            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd',
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                currentText: "Today",

                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        var buttonPane = $(inst.dpDiv).find('.ui-datepicker-buttonpane');

                        buttonPane.find('.ui-datepicker-current').off('click').on('click',
                            function() {
                                var today = new Date();
                                $(input).datepicker('setDate', today);
                                $.datepicker._hideDatepicker(input); //close after selecting
                                $(input).blur(); //prevent auto-focus/reopen
                            });
                    }, 1);
                }
            });
            //  $('.datepicker').datetimepicker();
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush
