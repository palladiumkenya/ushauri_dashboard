@extends('layouts.master')
@section('page-css')

@endsection

@section('main-content')
<!-- <div class="breadcrumb">
                <ul>
                    <li><a href="">Appointment Dashboard</a></li>
                    <li></li>
                </ul>
            </div> -->
@if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')


<div class="col">

    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control select2" id="partners" name="partner">
                        <option value="">Partner</option>
                        @foreach ($all_partners as $partner => $value)
                        <option value="{{ $partner }}"> {{ $value }}</option>
                        @endforeach
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control county  input-rounded input-sm select2" id="counties" name="county">
                        <option value="">County:</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">

                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value=""> Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">.</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="module" name="module">
                        <option value="">Module : </option>
                        <option value="DSD">DSD</option>
                        <option value="PMTCT">PMTCT</option>
                    </select>
                </div>
            </div>

            <div class='col'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-2">
                            <label for="firstName1">From</label>
                        </div>

                        <div class="col-md-10">

                            <input type="date" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-2">
                            <label for="firstName1">To</label>
                        </div>
                        <div class="col-md-10">

                            <input type="date" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="col-md-4">

                    </div>
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
@if (Auth::user()->access_level == 'Facility')


<div class="col">

    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">

            <div class="col-lg-3">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="module" name="module">
                        <option value="">Module : </option>
                        <option value="DSD">DSD</option>
                        <option value="PMTCT">PMTCT</option>
                    </select>
                </div>
            </div>

            <div class='col-lg-3'>
                <div class="form-group">
                    <div class="input-group">
                    <div class="col-md-2">
                            <label for="firstName1">From</label>
                        </div>
                        <div class="col-md-10">

                            <input type="date" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                    <div class="col-md-2">
                            <label for="firstName1">To</label>
                        </div>
                        <div class="col-md-10">

                            <input type="date" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">

                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">Dashboard</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-indicators" role="tab" aria-selected="false"></a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">


        <div id="highchart"></div>
        <div class="row">
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Registered Facilities </p>

                            <p id="facilities_ever_enrolled" class="text-primary text-20 line-height-1 mb-1">{{$facilities_ever_enrolled}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Active Facilities </p>

                            <p id="active_facilities" class="text-primary text-20 line-height-1 mb-1">{{count($active_facilities)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Clients Registered</p>

                            <p id="client_ever_enrolled" class="text-primary text-20 line-height-1 mb-2">{{number_format($client_ever_enrolled)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0"> Active Clients</p>
                            <a class="has-arrow" href="{{route('client_dashboard')}}">
                                <p id="client" class="text-primary text-20 line-height-1 mb-2">{{number_format($client)}}</p>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            @endif
            @if (Auth::user()->access_level == 'Facility')
            <div class="col-lg-6">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Clients Registered</p>

                            <p id="client_ever_enrolled" class="text-primary text-20 line-height-1 mb-2">{{number_format($client_ever_enrolled)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Active Clients</p>

                            <p id="client" class="text-primary text-20 line-height-1 mb-2">{{number_format($client)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>

        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="client_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="client_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>

    </div>
    <!-- main dashbaord ends -->

    <!-- client dashbaord starts -->

    <!-- client dashboard ends -->

    <div class="tab-pane fade" id="nav-indicators" role="tabpanel" aria-labelledby="nav-indicators-tab">

    </div>
</div>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>


<script type="text/javascript">
    // var filterForm = $("#dataFilter");
    // filterForm.submit(function(e) {
    //     e.preventDefault();
    //     var thisForm = $(this);
    //     var endPoint = thisForm.attr("action") || window.location.href;
    //     var method = thisForm.attr("method");
    //     var formData = thisForm.serialize();

    //     console.log(endPoint);
    //     console.log(method);
    //     Swal.fire({
    //         title: "Please wait",
    //         imageUrl: "/images/Ripple.gif",
    //         showConfirmButton: false,
    //         allowOutsideClick: false
    //     });

    //     this.submit();

    // });

    $('.partners').select2();
    $('.counties').select2();
    $('.subcounties').select2();

    var Clients_male = <?php echo json_encode($clients_male) ?>;
    var Clients_female = <?php echo json_encode($clients_female) ?>;
    var Unknown_gender = <?php echo json_encode($unknown_gender) ?>;
    var Client_to_nine = <?php echo json_encode($client_to_nine) ?>;
    var Client_to_fourteen = <?php echo json_encode($client_to_fourteen) ?>;
    var Client_to_nineteen = <?php echo json_encode($client_to_nineteen) ?>;
    var Client_to_twentyfour = <?php echo json_encode($client_to_twentyfour) ?>;
    var Client_to_twentyfive_above = <?php echo json_encode($client_to_twentyfive_above) ?>;
    var Client_unknown_age = <?php echo json_encode($client_unknown_age) ?>;


    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_dashboard_counties/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="county"]').empty();
                        $('select[name="county"]').append('<option value="">Please Select County</option>');
                        $.each(data, function(key, value) {

                            $('select[name="county"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="county"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_dashboard_sub_counties/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="subcounty"]').empty();
                        $('select[name="subcounty"]').append('<option value="">Please Select Sub County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="subcounty"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="subcounty"]').on('change', function() {
            var subcountyID = $(this).val();
            if (subcountyID) {
                $.ajax({
                    url: '/get_dashboard_facilities/' + subcountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });

    $('#dataFilter').on('submit', function(e) {
        e.preventDefault();
        let partners = $('#partners').val();
        let counties = $('#counties').val();
        let subcounties = $('#subcounties').val();
        let facilities = $('#facilities').val();
        let from = $('#from').val();
        let to = $('#to').val();
        let module = $('#module').val();

        Swal.fire({
                title: "Please wait, Loading Charts!",
                showConfirmButton: false,
                allowOutsideClick: false
            });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'GET',
            data: {
                "partners": partners,
                "counties": counties,
                "subcounties": subcounties,
                "facilities": facilities,
                "from": from,
                "to": to,
                "module": module
            },
            url: "{{ route('filter_dashboard_charts') }}",
            success: function(data) {

                $("#client").html(data.client);
                $("#client_ever_enrolled").html(data.client_ever_enrolled);
                $("#facilities_ever_enrolled").html(data.facilities_ever_enrolled);
                $("#active_facilities").html(data.active_facilities);
                Clients_male = parseInt(data.clients_male)
                Clients_female = parseInt(data.clients_female)
                Unknown_gender = parseInt(data.unknown_gender)
                Client_to_nine = parseInt(data.client_to_nine)
                Client_to_fourteen = parseInt(data.client_to_fourteen)
                Client_to_nineteen = parseInt(data.client_to_nineteen)
                Client_to_twentyfour = parseInt(data.client_to_twentyfour)
                Client_to_twentyfive_above = parseInt(data.client_to_twentyfive_above)
                Client_unknown_age = parseInt(data.client_unknown_age)



                clientGender.series[0].setData([Clients_male, Clients_female, Unknown_gender]);
                clientAge.series[0].setData([Client_to_nine, Client_to_fourteen, Client_to_nineteen, Client_to_twentyfour, Client_to_twentyfive_above, Client_unknown_age]);

                Swal.close();


            }
        });
    });



    var clientGender = Highcharts.chart('client_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Active Client By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'Uknown Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Clients'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            }
        },
        series: [{
            name: 'Gender',
            data: [Clients_male, Clients_female, Unknown_gender]
        }],

    });

    var clientAge = Highcharts.chart('client_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Active Client By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Clients'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            }
        },
        series: [{
            name: 'Age',
            data: [Client_to_nine, Client_to_fourteen, Client_to_nineteen, Client_to_twentyfour, Client_to_twentyfive_above, Client_unknown_age]
        }],

    });

    // var appointmentGender = Highcharts.chart('appointment_gender', {
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: 'Appointment By Gender'
    //     },
    //     xAxis: {
    //         categories: ['Male', 'Female', 'UKNOWN Gender']
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: 'Number of Appointments'
    //         },
    //         stackLabels: {
    //             enabled: true,
    //             style: {
    //                 fontWeight: 'bold',
    //                 color: ( // theme
    //                     Highcharts.defaultOptions.title.style &&
    //                     Highcharts.defaultOptions.title.style.color
    //                 ) || 'gray'
    //             }
    //         }
    //     },
    //     tooltip: {
    //         formatter: function() {
    //             return '<b>' + this.x + '</b><br/>' +
    //                 this.series.name + ': ' + this.y;
    //         }
    //     },
    //     plotOptions: {
    //         column: {
    //             stacking: 'normal',
    //         }
    //     },
    //     series: [{
    //         name: 'Gender',
    //         data: [Appointment_male, Appointment_female, Appointment_uknown_gender]
    //     }],

    // });

    // var appointmentAge = Highcharts.chart('appointment_age', {
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: 'Appointment By Age'
    //     },
    //     xAxis: {
    //         categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: 'Number of Appointments'
    //         },
    //         stackLabels: {
    //             enabled: true,
    //             style: {
    //                 fontWeight: 'bold',
    //                 color: ( // theme
    //                     Highcharts.defaultOptions.title.style &&
    //                     Highcharts.defaultOptions.title.style.color
    //                 ) || 'gray'
    //             }
    //         }
    //     },
    //     tooltip: {
    //         formatter: function() {
    //             return '<b>' + this.x + '</b><br/>' +
    //                 this.series.name + ': ' + this.y;
    //         }
    //     },
    //     plotOptions: {
    //         column: {
    //             stacking: 'normal',
    //         }
    //     },
    //     series: [{
    //         name: 'Age',
    //         data: [Appointment_to_nine, Appointment_to_fourteen, Appointment_to_nineteen, Appointment_to_twentyfour, Appointment_to_twentyfive_above, Appointment_uknown_age]
    //     }],

    // });

    // // missed appointment charts
    // var totalmissedappointmentGender = Highcharts.chart('total_missed_appointment_gender', {
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: 'Total Missed Appointment By Gender'
    //     },
    //     xAxis: {
    //         categories: ['Male', 'Female', 'UKNOWN Gender']
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: 'No of Missed Appointments'
    //         },
    //         stackLabels: {
    //             enabled: true,
    //             style: {
    //                 fontWeight: 'bold',
    //                 color: ( // theme
    //                     Highcharts.defaultOptions.title.style &&
    //                     Highcharts.defaultOptions.title.style.color
    //                 ) || 'gray'
    //             }
    //         }
    //     },
    //     tooltip: {
    //         formatter: function() {
    //             return '<b>' + this.x + '</b><br/>' +
    //                 this.series.name + ': ' + this.y;
    //         }
    //     },
    //     plotOptions: {
    //         column: {
    //             stacking: 'normal',
    //         }
    //     },
    //     series: [{
    //         name: 'Gender',
    //         data: [Appointment_total_missed_male, Appointment_total_missed_female, Appointment_total_missed_uknown_gender]
    //     }],

    // });

    // var totalmissedappointmentAge = Highcharts.chart('total_missed_appointment_age', {
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: 'Total Missed Appointment By Age'
    //     },
    //     xAxis: {
    //         categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: 'No of Missed Appointments'
    //         },
    //         stackLabels: {
    //             enabled: true,
    //             style: {
    //                 fontWeight: 'bold',
    //                 color: ( // theme
    //                     Highcharts.defaultOptions.title.style &&
    //                     Highcharts.defaultOptions.title.style.color
    //                 ) || 'gray'
    //             }
    //         }
    //     },
    //     tooltip: {
    //         formatter: function() {
    //             return '<b>' + this.x + '</b><br/>' +
    //                 this.series.name + ': ' + this.y;
    //         }
    //     },
    //     plotOptions: {
    //         column: {
    //             stacking: 'normal',
    //         }
    //     },
    //     series: [{
    //         name: 'Age',
    //         data: [Appointment_total_missed_to_nine, Appointment_total_missed_to_fourteen, Appointment_total_missed_to_nineteen, Appointment_total_missed_to_twentyfour, Appointment_total_missed_to_twentyfive_above, Appointment_total_missed_uknown_age]
    //     }],

    // });





    var colors = Highcharts.getOptions().colors;
</script>





<!-- end of col -->

@endsection