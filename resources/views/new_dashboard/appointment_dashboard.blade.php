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
                <div class='col'>
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
            <div class='col-lg-3'>
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
        <a class="nav-item nav-link active" id="nav-appointment-tab" data-toggle="tab" href="#nav-appointment" role="tab" aria-controls="nav-appointmen" aria-selected="true">Appointments</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-indicators" role="tab" aria-selected="false"></a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">

        <div class="row">
            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Appointments</p>

                            <p id="appointment" class="text-primary text-20 line-height-1 mb-2">{{number_format($appointment)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Honored</p>

                            <p id="appointment_honoured" class="text-primary text-20 line-height-1 mb-2">{{number_format($appointment_honoured)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Not Honored</p>

                            <p id="appointment_not_honoured" class="text-primary text-20 line-height-1 mb-2">{{number_format($appointment_not_honoured)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Future Appointments</p>

                            <p id="appointment_not_honoured" class="text-primary text-20 line-height-1 mb-2">{{number_format($all_future_apps)}}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_honoured_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_honoured_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_not_honoured_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_not_honoured_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>

    </div>
    <!-- main dashbaord ends -->
    <div class="tab-pane fade" id="nav-indicators" role="tabpanel" aria-labelledby="nav-indicators-tab">
        @if (count($indicator) > 0)
        @foreach($indicator as $result)
        <h6>{{$result->name}}
            <h6>
                <h6>{{$result->description}}
                    <h6>
                        @endforeach
                        @endif
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
    $('.partners').select2();
    $('.counties').select2();
    $('.subcounties').select2();



    var Appointment_honoured_male = <?php echo json_encode($appointment_honoured_male) ?>;
    var Appointment_honoured_female = <?php echo json_encode($appointment_honoured_female) ?>;
    var Appointment_honoured_uknown_gender = <?php echo json_encode($appointment_honoured_uknown_gender) ?>;
    var Appointment_honored_to_nine = <?php echo json_encode($appointment_honored_to_nine) ?>;
    var Appointment_honored_to_fourteen = <?php echo json_encode($appointment_honored_to_fourteen) ?>;
    var Appointment_honored_to_nineteen = <?php echo json_encode($appointment_honored_to_nineteen) ?>;
    var Appointment_honored_to_twentyfour = <?php echo json_encode($appointment_honored_to_twentyfour) ?>;
    var Appointment_honored_to_twentyfive_above = <?php echo json_encode($appointment_honored_to_twentyfive_above) ?>;
    var Appointment_honored_to_uknown_age = <?php echo json_encode($appointment_honored_to_uknown_age) ?>;
    var Appointment_not_honoured_male = <?php echo json_encode($appointment_not_honoured_male) ?>;
    var Appointment_not_honoured_female = <?php echo json_encode($appointment_not_honoured_female) ?>;
    var Appointment_not_honoured_uknown_gender = <?php echo json_encode($appointment_not_honoured_uknown_gender) ?>;
    var Appointment_not_honored_to_nine = <?php echo json_encode($appointment_not_honored_to_nine) ?>;
    var Appointment_not_honored_to_fourteen = <?php echo json_encode($appointment_not_honored_to_fourteen) ?>;
    var Appointment_not_honored_to_nineteen = <?php echo json_encode($appointment_not_honored_to_nineteen) ?>;
    var Appointment_not_honored_to_twentyfour = <?php echo json_encode($appointment_not_honored_to_twentyfour) ?>;
    var Appointment_not_honored_to_twentyfive_above = <?php echo json_encode($appointment_not_honored_to_twentyfive_above) ?>;
    var Appointment_not_honored_to_uknown_age = <?php echo json_encode($appointment_not_honored_to_uknown_age) ?>;



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
            url: "{{ route('filter_appointment_charts') }}",
            success: function(data) {
                $("#appointment").html(data.appointment);
                $("#appointment_honoured").html(data.appointment_honoured);
                $("#appointment_not_honoured").html(data.appointment_not_honoured);

                Appointment_honoured_male = parseInt(data.appointment_honoured_male)

                console.log(Appointment_honoured_male);
                Appointment_honoured_female = parseInt(data.appointment_honoured_female)
                Appointment_honoured_uknown_gender = parseInt(data.appointment_honoured_uknown_gender)
                Appointment_honored_to_nine = parseInt(data.appointment_honored_to_nine)
                Appointment_honored_to_fourteen = parseInt(data.appointment_honored_to_fourteen)
                Appointment_honored_to_nineteen = parseInt(data.appointment_honored_to_nineteen)
                Appointment_honored_to_twentyfour = parseInt(data.appointment_honored_to_twentyfour)
                Appointment_honored_to_twentyfive_above = parseInt(data.appointment_honored_to_twentyfive_above)
                Appointment_honored_to_uknown_age = parseInt(data.appointment_honored_to_uknown_age)
                Appointment_not_honoured_male = parseInt(data.appointment_not_honoured_male)
                Appointment_not_honoured_female = parseInt(data.appointment_not_honoured_female)
                Appointment_not_honoured_uknown_gender = parseInt(data.appointment_not_honoured_uknown_gender)
                Appointment_not_honored_to_nine = parseInt(data.appointment_not_honored_to_nine)
                Appointment_not_honored_to_fourteen = parseInt(data.appointment_not_honored_to_fourteen)
                Appointment_not_honored_to_nineteen = parseInt(data.appointment_not_honored_to_nineteen)
                Appointment_not_honored_to_twentyfour = parseInt(data.appointment_not_honored_to_twentyfour)
                Appointment_not_honored_to_twentyfive_above = parseInt(data.appointment_not_honored_to_twentyfive_above)
                Appointment_not_honored_to_uknown_age = parseInt(data.appointment_not_honored_to_uknown_age)

                appointment_honouredGender.series[0].setData([Appointment_honoured_male, Appointment_honoured_female, Appointment_honoured_uknown_gender]);
                appointment_honouredAge.series[0].setData([Appointment_honored_to_nine, Appointment_honored_to_fourteen, Appointment_honored_to_nineteen, Appointment_honored_to_twentyfour, Appointment_honored_to_twentyfive_above, Appointment_honored_to_uknown_age]);
                appointment_not_honouredGender.series[0].setData([Appointment_not_honoured_male, Appointment_not_honoured_female, Appointment_not_honoured_uknown_gender]);
                appointment_not_honouredAge.series[0].setData([Appointment_not_honored_to_nine, Appointment_not_honored_to_fourteen, Appointment_not_honored_to_nineteen, Appointment_not_honored_to_twentyfour, Appointment_not_honored_to_twentyfive_above, Appointment_not_honored_to_uknown_age]);
                Swal.close();
            }
        });
    });


    //APPOINTMENT HONOURED GENDER
    var appointment_honouredGender = Highcharts.chart('appointment_honoured_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Honored By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Honored'
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
            data: [Appointment_honoured_male, Appointment_honoured_female, Appointment_honoured_uknown_gender]
        }],

    });
    // APPOINTMENT HONOURED AGE
    var appointment_honouredAge = Highcharts.chart('appointment_honoured_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Honored By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Honored'
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
            data: [Appointment_honored_to_nine, Appointment_honored_to_fourteen, Appointment_honored_to_nineteen, Appointment_honored_to_twentyfour, Appointment_honored_to_twentyfive_above, Appointment_honored_to_uknown_age]
        }],

    });

    //APPOINTMENT NOT HONOURED GENDER
    var appointment_not_honouredGender = Highcharts.chart('appointment_not_honoured_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Not Honored By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Not Honored'
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
            data: [Appointment_not_honoured_male, Appointment_not_honoured_female, Appointment_not_honoured_uknown_gender]
        }],

    });
    // APPOINTMENT NOT HONOURED AGE
    var appointment_not_honouredAge = Highcharts.chart('appointment_not_honoured_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Not Honored By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Not Honored'
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
            data: [Appointment_not_honored_to_nine, Appointment_not_honored_to_fourteen, Appointment_not_honored_to_nineteen, Appointment_not_honored_to_twentyfour, Appointment_not_honored_to_twentyfive_above, Appointment_not_honored_to_uknown_age]
        }],

    });



    var colors = Highcharts.getOptions().colors;
</script>





<!-- end of col -->

@endsection