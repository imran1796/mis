@extends('layouts.app', [
    'activePage' => 'vesselInfo-index',
    'title' => 'GLA Admin',
    'navName' => 'MLO Wise Data',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Mlo Wise Data</h2>
                        </div>


                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-header">
                        {{-- start auto search --}}
                        <div class="form-group">
                            <label for="mr" class="sr-only">Auto Search</label>
                            <input value="" type="text" name="autosearch" id="autosearch"
                                class="form-control form-control-sm" placeholder="Auto Search ">
                        </div>
                        {{-- end auto search --}}
                    </div>
                    <div class="card-body">
                        <table class="tableFixHead table-bordered table2excel custom-table-report mb-3 table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>MLO TEUS</th>
                                    <th>VESSEL TEUS</th>

                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($data as $mlo) --}}
                                <tr>
                                    <td>{{ $data['mlo']->sum('dc20') }}</td>
                                    <td>{{ $data['vessel']->sum('dc20') }}</td>
                                </tr>
                                {{-- @endforeach --}}
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $("#autosearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".tableFixHead tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endpush
