$(document).ready(function (e) {
    $("#additional_free_time").on("change paste keyup", function () {
        berth_date = $("#berth_date").val();
        basic_free = parseInt($("#basic_free_time").val());
        additional_free = parseInt($("#additional_free_time").val());
        total =basic_free+ additional_free;

        var dateAr = berth_date.split('-');
        var newDate = dateAr[1] + '/' + dateAr[0] + '/' + dateAr[2];
        var someDate = new Date(newDate);
        someDate.setDate(someDate.getDate() + total);
        var date = someDate.getMonth()+1 + '/' + someDate.getDate() + '/' + someDate.getFullYear();

        $('#return_date').val(date);
        //  $("input").css("background-color", "pink");
    });
    $('#check').on('click', (function (e) {
        e.preventDefault()
        var bl = $('#bl').val();
        $.ajax({
            type: 'POST',
            url: '{{ route('search') }}',
            data: {"search": bl, "_token": "{{ csrf_token() }}",},
            /* headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
*/
            success: function (data) {
                if (data.data != null) {
                    console.log(data.data.container.ctn_type)
                    console.log(data)
                    // var respons = JSON.parse(JSON.stringify(data));

                    $('#berth_date').val(data.data.vessel_info.arrival_date)
                    $('#notify').val(data.data.notify.name)
                    if(data.data.offdock.is_depot == 1){
                        $( "#lift_on_charge" ).prop( "checked", true );
                    }else {
                        $( "#lift_on_charge" ).prop( "checked", false );

                    }

                    var container = data.data.container;
                    //     console.log(container);
                    var refer = 0
                    var ctn_str =''
                    const counts = {};
                    ctn_array = new Array()

                    for (const property in container) {
                        var ctn = container[property].ctn_type;
                        //  console.log(ctn);

                        if (ctn.search('R') != -1) {
                            console.log(container[property].ctn_type);
                            refer++;
                        }else {
                            $('#special_cleaning_charge').val('')
                            $( "#plugging_charge" ).prop( "checked", false );
                            $( "#monitoring_charge" ).prop( "checked", false );
                        }
                        //   ctn_str = ctn_str + container[property].ctn_type+','
                        ctn_array.push(ctn_str + container[property].ctn_type)

                    }

                    ctn_array.forEach(function (x) { counts[x] = (counts[x] || 0) + 1; });
                    $('.summary').html('<strong>BL Summary:- Container: ' + container.length+' || Container Type: ' + JSON.stringify(counts)+
                        '</strong>')
                    $('.summary').show()

                    if(refer>0){
                        $( "#plugging_charge" ).prop( "checked", true );
                        $( "#monitoring_charge" ).prop( "checked", true );
                        $('#special_cleaning_charge').val(refer * data.charge.amount)

                    }

                    console.log(data.data.vessel_info.arrival_date)
                    console.log(data.vessel_info)

                    $.notify({
                        icon: "add_alert",
                        message: "WOW!!! U Did IT"

                    }, {
                        type: 'success',
                        timer: 2000,
                        placement: {
                            from: 'top',
                            align: 'right',
                        }
                    });
                    /*setTimeout(
                        function()
                        {
                            window.location.href= "{{ route('permissions.index') }}"
                                    }, 1000)*/
                }
            },
            error: function (data) {
                //ar response = $.parseJSON(data.responseText);
                $.each(data.errors, function (key, val) {
                    $.notify({
                        icon: "add_alert",
                        message: val[0]

                    }, {
                        type: 'danger',
                        timer: 1000,
                        placement: {
                            from: 'top',
                            align: 'right',
                        }
                    });
                })

            }
        });
    }));


    /*      $(".datepicker").datepicker({
              altFormat: "yy-mm-dd"
          });*/

    $('#postForm').on('submit', (function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: '{{ route('principles.store') }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            enctype: 'multipart/form-data',
            success: function (data) {
                console.log(data)
                $.notify({
                    icon: "add_alert",
                    message: "WOW!!! U Did IT"

                }, {
                    type: 'success',
                    timer: 2000,
                    placement: {
                        from: 'top',
                        align: 'right',
                    }
                });
                /*setTimeout(
                    function()
                    {
                        window.location.href= "{{ route('permissions.index') }}"
                                    }, 1000)*/
            },
            error: function (data) {
                var response = $.parseJSON(data.responseText);
                $.each(response.errors, function (key, val) {
                    $.notify({
                        icon: "add_alert",
                        message: val[0]

                    }, {
                        type: 'danger',
                        timer: 1000,
                        placement: {
                            from: 'top',
                            align: 'right',
                        }
                    });
                })

            }
        });
    }));
});
