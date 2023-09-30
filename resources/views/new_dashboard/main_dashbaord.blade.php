@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<style rel="stylesheet" type="text/css">
    .no_count {
        font-weight: 700;
        font-size: 24px;
    }

    .Clients {

        background: #FFFFFF;
        border-radius: 10px;

    }

    .Txcurr {
        background: #FFFFFF;
        border-radius: 10px;
    }
</style>


@endsection

@section('main-content')

@if (Auth::user()->access_level == 'Facility')
<div class="row">
    <div style="margin-bottom:10px; ">
        <div class="Search_Modal" style="display: inline;">
            <!-- Button to Open the Modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"> <i class="fa fa-search"></i>
                OTP Search
            </button>
        </div>
    </div>

</div>

<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Search Client OTP</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form role="form" id="searchForm" method="get" action="{{ route('otp_search') }}">
                {{ csrf_field() }}

                <!-- Modal body -->
                <div class="modal-body">

                    <div class="search_field">
                        <input type="text" class="upn_search form-control" id="upn_search" name="upn_search" placeholder="Please Enter Phone No e.i 0700000000: " />
                    </div>

                    <div class="loading_div" style="display: none;">
                        <span>Loading ....Please wait .....</span>
                    </div>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="search_upn_btn btn btn-default pull-left"><i class=" fa fa-search"></i>Search</button>
                    <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-stop-circle-o"></i>Close</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- The Second Modal -->
<div class="modal" id="secondModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Second Modal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <p id="otpNumber"></p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-stop-circle-o"></i>Close</button>
            </div>
        </div>
    </div>
</div>

@endif
@if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')


<div class=" col">

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

            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Registered Facilities </span>

                            <p id="facilities_ever_enrolled" class="no_count">{{number_format($facilities_ever_enrolled)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Active Facilities </span>

                            <p id="active_facilities" class="no_count">{{number_format(count($active_facilities))}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Clients Registered</span>

                            <p id="client_ever_enrolled" class="no_count">{{number_format($client_ever_enrolled)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 ">
                <div class="Txcurr card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle"> TX CURR</span>
                            <p id="client" class="no_count">{{number_format($client)}}</p>

                        </div>
                    </div>
                </div>
            </div>

            @endif
            @if (Auth::user()->access_level == 'Facility')
            <div class="col-sm-6">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Clients Registered</span>
                            <p id="client_ever_enrolled" class="no_count" style="margin-top:2px;">{{number_format($client_ever_enrolled)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="Txcurr card o-hidden mb-0 h-75">
                    <div class="card-body ">
                        <div class="content">
                            <span class="card_tittle">TX CURR</span>

                            <p id="client" class="no_count" style="margin-top:2px;">{{number_format($client)}}</p>
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

        <div class="col-md-12">
            <div class="row">
                <input id="authent" type="hidden" value="{{ auth()->user()->access_level }}">
                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Partner')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>
                            <div class="table-responsive">
                                <table id="table_summary" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Facility</th>
                                            <th>MFL Code</th>
                                            <th>County</th>
                                            <th>Partner</th>
                                            <th>Clients</th>
                                            <th>Consented</th>
                                            <th>Tx Curr</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>

                                </table>

                            </div>

                        </div>
                        @endif


                    </div>
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
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
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

<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>




<script type="text/javascript">
    let authent = $('#authent').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var $j = jQuery.noConflict();
    $.ajax({
        type: 'GET',
        url: "{{ route('partner_summary') }}",
        success: function(data) {
            console.log(data);
            if (authent == 'Partner') {
                var list = data.result;

                $.each(list, function(index, item) {

                    $('#table_summary tbody').append('<tr><td>' + item.facility + '</td><td>' + item.mfl_code + '</td><td>' + item.county + '</td><td>' + item.partner + '</td><td>' + item.client_ever_enrolled + '</td><td>' + item.client_consented + '</td><td>' + item.tx_cur + '</td></tr>');
                });
                $j('#table_summary').DataTable({
                    columnDefs: [{
                        targets: [0],
                        orderData: [0, 1]
                    }, {
                        targets: [1],
                        orderData: [1, 0]
                    }, {
                        targets: [4],
                        orderData: [4, 0]
                    }],
                    "pageLength": 10,
                    "paging": true,
                    "responsive": true,
                    "ordering": true,
                    "info": true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });
            }
        }
    });
    $('#searchForm').submit(function(e) {
        e.preventDefault();
        var searchValue = $('#upn_search').val();

        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            data: {
                upn_search: searchValue

            },

            beforeSend: function() {
                $('.loading_div').show();
            },
            success: function(response) {
                $('.loading_div').hide();
                if (response.profile_otp_number) {
                    swal({
                        title: "OTP Number Found",
                        text: "The OTP number is: " + response.profile_otp_number,
                        icon: "success",
                        button: "OK",
                    });
                    $('#myModal').modal('hide');
                } else {
                    swal({
                        title: "No OTP Number Found",
                        text: "No OTP number found for the given phone number.",
                        icon: "warning",
                        button: "OK",
                    });
                }
            },
            error: function() {
                $('.loading_div').hide();
                swal({
                    title: "Error",
                    text: "An error occurred while searching. Please try again.",
                    icon: "error",
                    button: "OK",
                });
            }
        });
    });

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

    $j('.partners').select2();
    $j('.counties').select2();
    $j('.subcounties').select2();

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
                // if (authent == 'Partner') {
                //     var table = $j('#table_summary').DataTable();

                //     // Destroy the DataTable instance
                //     table.destroy();

                //     $('#table_summary tbody').empty();
                //     var list = data.result;

                //     $.each(list, function(index, item) {

                //         $('#table_summary tbody').append('<tr><td>' + item.facility + '</td><td>' + item.mfl_code + '</td><td>' + item.county + '</td><td>' + item.partner + '</td><td>' + item.client_ever_enrolled + '</td><td>' + item.client_consented + '</td><td>' + item.tx_cur + '</td></tr>');
                //     });
                //     $('#table_summary').DataTable({
                //         columnDefs: [{
                //             targets: [0],
                //             orderData: [0, 1]
                //         }, {
                //             targets: [1],
                //             orderData: [1, 0]
                //         }, {
                //             targets: [4],
                //             orderData: [4, 0]
                //         }],
                //         "pageLength": 10,
                //         "paging": true,
                //         "responsive": true,
                //         "ordering": true,
                //         "info": true,
                //         dom: 'Bfrtip',
                //         buttons: [
                //             'copyHtml5',
                //             'excelHtml5',
                //             'csvHtml5',
                //             'pdfHtml5'
                //         ]
                //     });
                // }
                $("#client").html(data.client.toLocaleString());
                $("#client_ever_enrolled").html(data.client_ever_enrolled.toLocaleString());
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
            text: 'Registered Clients By Gender',
            style: {
                fontFamily: 'Inter',
                fontSize: '14px'
            }
        },
        style: {
            fontFamily: 'Inter'
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
            color: '#01058A',
            data: [Clients_male, Clients_female, Unknown_gender]
        }],

    });

    var clientAge = Highcharts.chart('client_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Registered Clients By Age',
            style: {
                fontFamily: 'Inter',
                fontSize: '14px'
            }
        },
        style: {
            fontFamily: 'Inter'
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
            color: '#01058A',
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