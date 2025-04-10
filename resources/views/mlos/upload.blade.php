@extends('layouts.app', [
    'activePage' => 'mlo',
    'title' => 'GLA Admin',
    'navName' => 'MLO',
    'activeButton' => 'laravel',
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="section-image">
                <div class="row mb-2">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>MLO Wise Data</h2>
                        </div>
                    </div>
                </div>

                <div class="card bg-white">
                    <div class="card-body">
                        <div class="form">
                            
                        </div>
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

            // $('#createMloModalForm').on('submit', function(e) {
            //     e.preventDefault();

            //     let formData = new FormData(this);
            //     console.log(formData);

            //     $.ajax({
            //         type: 'POST',
            //         url: '{{ route('mlos.store') }}',
            //         data: formData,
            //         cache: false,
            //         contentType: false,
            //         processData: false,
            //         enctype: 'multipart/form-data',
            //         success: function(response) {
            //             $('#createMloModal').modal('hide');
            //             demo.customShowNotification('success', response.success);
            //             window.location.reload();
            //         },
            //         error: function(response) {
            //             if (response.responseJSON.error) {
            //                 demo.customShowNotification('danger', response.responseJSON.error);
            //             }
            //             for (let field in response.responseJSON.errors) {
            //                 for (let i = 0; i < response.responseJSON.errors[field]
            //                     .length; i++) {
            //                     demo.customShowNotification('danger', response.responseJSON
            //                         .errors[field][i]);
            //                 }
            //             }
            //         }
            //     });
            // });
        });
    </script>
@endpush
