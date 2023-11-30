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
        position: relative;

    }

    .radial-01 {
        top: 15px;
        right: 15px;
        float: right;
        position: absolute;
        width: 40px;
        height: 40px;
        text-align: center;
        border-radius: 100%;
        /* background-color: transparent; */
        /* overflow: hidden; */

    }

    .radial-01 p {
        position: absolute;
        left: -25px;
        bottom: -55px;
        z-index: 50;
        width: 100px;
    }

    .radial-01 .radial-01-number {
        position: absolute;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        background-color: #fff;
        border-radius: 100%;
        padding-top: 11px;
        z-index: 20;
        /* background-color: transparent; */
    }

    .radial-01 .radial-01-number {
        font-weight: 700;
        font-size: 10px;
    }

    .radial-01 .radial-01-number .radial-01-syb {
        font-weight: 700;
        font-size: 10px;
    }

    .radial-01>span.radial-01-border-r:before {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #ccc;
        border-radius: 100%;
        z-index: 5;
    }

    .radial-01>span.radial-01-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #006838;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 15px);
    }

    .radial-01>span.radial-02-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #97080F;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 30px);
    }

    .radial-01>span.radial-03-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #01058A;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 40px);
    }

    .radial-01>span.radial-01-border-l:before {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #ccc;
        border-radius: 100%;
        z-index: 5;
    }

    /* .radial-01 .progress-bar {
        background: conic-gradient(#3498db 0% var(--progress, 0%), transparent var(--progress, 0%) 100%);
    } */



    /* Customize colors as per your design */
</style>

@endsection

@section('main-content')
<!-- <div class="breadcrumb">
                <ul>
                    <li><a href="">Appointment Dashboard</a></li>
                    <li></li>
                </ul>
            </div> -->
@if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control select2" id="partners" name="partner">
                        <option value="">Partner</option>
                        @if (count($partners) > 0)
                        @foreach($partners as $partner)
                        <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                        @endforeach
                        @endif
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option value="">County</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value="">Sub County</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value="">Facility</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class='col-lg-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
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
@if (Auth::user()->access_level == 'County')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control select2" id="partners" name="partner">
                        <option value="">Partner</option>
                        @if (count($partners) > 0)
                        @foreach($partners as $partner)
                        <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                        @endforeach
                        @endif
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option value="">County</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value="">Sub County</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value="">Facility</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class='col-lg-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-14">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-14">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
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
@if (Auth::user()->access_level == 'Partner')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option value="">County</option>
                        @if (count($counties) > 0)
                        @foreach($counties as $county)
                        <option value="{{$county->id }}">{{ ucwords($county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value="">Sub County</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value="">Facility</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class='col-lg-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
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
@if (Auth::user()->access_level == 'Sub County')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value=""></option>
                        @if (count($facilities) > 0)
                        @foreach($facilities as $facility)
                        <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class='col-lg-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
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
            <div class='col-lg-4'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" pattern="\d{4}/\d{2}/\d{2}" />

                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" pattern="\d{4}/\d{2}/\d{2}" onkeydown="return false" />

                            <!-- <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" /> -->
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
        <a class="nav-item nav-link active" id="nav-client-tab" data-toggle="tab" href="#nav-client" role="tab" aria-controls="nav-client" aria-selected="true">Nishauri Dashboard</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-indicators" role="tab" aria-selected="false"></a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-client" role="tabpanel" aria-labelledby="nav-client-tab">

        <div class="row">
            <div class="col-sm-3 ">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">TX CURR</span>
                            <p id="client" class="no_count"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Clients in Ushauri</span>
                            <p id="client_ushauri" class="no_count"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Clients Enrolled</span>
                            <p id="client_enrolled" class="no_count"></p>
                        </div>
                        <div class="radial-01 radial-three-quarters">
                            <span id="enrolledPercentage" class="radial-01-number">
                                <span class="radial-01-syb"><sup></sup></span>
                            </span>
                            <span class="radial-01-border-r"></span>
                            <span class="radial-01-border-l"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="Clients card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_tittle">Not Enrolled</span>

                            <p id="client_profile" class="no_count"></p>
                        </div>
                        <div class="radial-01 radial-three-quarters">
                            <span id="notEnrolledPercentage" class="radial-01-number">
                                <span class="radial-01-syb"><sup></sup></span>
                            </span>
                            <span class="radial-02-border-r"></span>
                            <span class="radial-01-border-l"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <input id="authent" type="hidden" value="{{ auth()->user()->access_level }}">
            <div class="col-6">

                <div class="card-body row">
                    <div id="enrollment_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="enrollment_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-12">

                <div class="card-body row">
                    <div id="enrollment_monthly" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-12">

                <div class="card-body row">
                    <div id="module_access" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <!-- <div class="col-12">

                <div class="card-body row">
                    <div id="daily_login" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div> -->
            @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
            <div class="col-12">
                <div class="card-body row">
                    <div id="enrollment_facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            @endif
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
            <div class="col-12">
                <div class="card-body row">
                    <div id="enrollment_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Facility')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>

                            <div class="table-responsive">
                                <table id="table_client" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>NUPI No</th>
                                            <th>Clinic No</th>
                                            <th>Client Name</th>
                                            <th>DOB</th>
                                            <th>Phone No</th>
                                            <th>Gender</th>
                                            <th>Enrollment</th>
                                            <th>Last Login</th>
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

<!-- Sweet alert -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


<script type="text/javascript">
    $('.partners').select2();
    $('.counties').select2();
    $('.subcounties').select2();

    let authenticating = $('#authent').val();
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
                        $('select[name="county"]').append('<option value="">Please County</option>');
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
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
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
    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_county_facilities/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
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

    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_partner_sub_counties/' + partnerID,
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
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_partner_facilities/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
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
    var $j = jQuery.noConflict();
    Swal.fire({
        title: "Please wait loading...",
        showConfirmButton: false,
        allowOutsideClick: false
    });
    $.ajax({
        type: 'GET',
        url: "{{ route('nishauri_uptake') }}",
        success: function(data) {
            var allEnrollments = data.all_enrollment;
            console.log(allEnrollments);
            var enrolledCount = 0;
            var notenrolledCount = 0;

            if (authenticating == 'Admin' || authenticating == 'Donor' || authenticating == 'Partner' || authenticating == 'County' || authenticating == 'Sub County') {
                function getCountsByGender(allEnrollments) {
                    const counts = {
                        Male: {
                            enrolled: 0,
                            not_enrolled: 0
                        },
                        Female: {
                            enrolled: 0,
                            not_enrolled: 0
                        },
                        Unknown: {
                            enrolled: 0,
                            not_enrolled: 0
                        }
                    };

                    data.all_enrollment.forEach(entry => {
                        const {
                            gender,
                            no_of_clients,
                            enrollment
                        } = entry;
                        if (enrollment === 'Enrolled') {
                            counts[gender].enrolled += no_of_clients;
                        } else if (enrollment === 'Not Enrolled') {
                            counts[gender].not_enrolled += no_of_clients;
                        }
                    });

                    return counts;
                }

                function getCountsByAge(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const age = entry.age_range;
                        if (!counts[age]) {
                            counts[age] = {
                                age_enrolled: 0,
                                age_not_enrolled: 0
                            };
                        }
                        if (entry.enrollment === 'Enrolled') {
                            counts[age].age_enrolled += entry.no_of_clients;
                        } else if (entry.enrollment === 'Not Enrolled') {
                            counts[age].age_not_enrolled += entry.no_of_clients;
                        }
                    });
                    return counts;
                }

                function getCountsByMonthly(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const monthly = entry.enrolled_month;
                        if (monthly !== null) {
                            if (!counts[monthly]) {
                                counts[monthly] = {
                                    monthly_enrolled: 0,
                                    monthly_not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[monthly].monthly_enrolled += entry.no_of_clients;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[monthly].monthly_not_enrolled += entry.no_of_clients;
                            }
                        }
                    });
                    return counts;
                }
                if (authenticating == 'Admin' || authenticating == 'Donor') {
                    function getCountsByPartner(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const partner = entry.partner;
                            if (!counts[partner]) {
                                counts[partner] = {
                                    enrolled: 0,
                                    not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[partner].enrolled += entry.no_of_clients;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[partner].not_enrolled += entry.no_of_clients;
                            }
                        });
                        return counts;
                    }
                } else {
                    function getCountsByFacility(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const facility = entry.facility;
                            if (!counts[facility]) {
                                counts[facility] = {
                                    enrolled: 0,
                                    not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[facility].enrolled += entry.no_of_clients;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[facility].not_enrolled += entry.no_of_clients;
                            }
                        });
                        return counts;
                    }
                }

                for (let i = 0; i < allEnrollments.length; i++) {
                    if (allEnrollments[i].enrollment === "Enrolled") {
                        enrolledCount += allEnrollments[i].no_of_clients;
                    } else {
                        notenrolledCount += allEnrollments[i].no_of_clients;
                    }
                }
                var enrolledPercentage = 0;
                var notEnrolledPercentage = 0;
                var totalCount = enrolledCount + notenrolledCount;
                if (totalCount !== 0) {
                    enrolledPercentage = (enrolledCount / totalCount) * 100;
                    notEnrolledPercentage = (notenrolledCount / totalCount) * 100;
                }


            } else {

                function getCountsByGender(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const gender = entry.gender;
                        if (!counts[gender]) {
                            counts[gender] = {
                                enrolled: 0,
                                not_enrolled: 0
                            };
                        }
                        if (entry.enrollment === 'Enrolled') {
                            counts[gender].enrolled++;
                        } else if (entry.enrollment === 'Not Enrolled') {
                            counts[gender].not_enrolled++;
                        }
                    });
                    return counts;
                }

                function getCountsByAge(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const age = entry.age_range;
                        if (!counts[age]) {
                            counts[age] = {
                                age_enrolled: 0,
                                age_not_enrolled: 0
                            };
                        }
                        if (entry.enrollment === 'Enrolled') {
                            counts[age].age_enrolled++;
                        } else if (entry.enrollment === 'Not Enrolled') {
                            counts[age].age_not_enrolled++;
                        }
                    });
                    return counts;
                }

                function getCountsByMonthly(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const monthly = entry.enrolled_month;
                        if (monthly !== null) {
                            if (!counts[monthly]) {
                                counts[monthly] = {
                                    monthly_enrolled: 0,
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[monthly].monthly_enrolled++;
                            }
                        }
                    });
                    return counts;
                }

                function getCountsByFacility(data) {
                    const counts = {};
                    data.forEach(entry => {
                        const facility = entry.facility;
                        if (!counts[facility]) {
                            counts[facility] = {
                                enrolled: 0,
                                not_enrolled: 0
                            };
                        }
                        if (entry.enrollment === 'Enrolled') {
                            counts[facility].enrolled++;
                        } else if (entry.enrollment === 'Not Enrolled') {
                            counts[facility].not_enrolled++;
                        }
                    });
                    return counts;
                }


                // function getCountsByPartner(data) {
                //     const counts = {};
                //     data.forEach(entry => {
                //         const partner = entry.partner;
                //         if (!counts[partner]) {
                //             counts[partner] = {
                //                 enrolled: 0,
                //                 not_enrolled: 0
                //             };
                //         }
                //         if (entry.enrollment === 'Enrolled') {
                //             counts[partner].enrolled++;
                //         } else if (entry.enrollment === 'Not Enrolled') {
                //             counts[partner].not_enrolled++;
                //         }
                //     });
                //     return counts;
                // }

                for (var i = 0; i < allEnrollments.length; i++) {
                    if (allEnrollments[i].enrollment === "Enrolled") {
                        enrolledCount++;
                    } else {
                        notenrolledCount++;
                    }
                }

                var enrolledPercentage = 0;
                var notEnrolledPercentage = 0;
                var totalCount = enrolledCount + notenrolledCount;
                if (totalCount !== 0) {
                    enrolledPercentage = (enrolledCount / totalCount) * 100;
                    notEnrolledPercentage = (notenrolledCount / totalCount) * 100;
                }
            }

            // gender
            const counts = getCountsByGender(allEnrollments);
            const gender = Object.keys(counts);
            const enrolled = gender.map(g => counts[g].enrolled);
            const not_enrolled = gender.map(g => counts[g].not_enrolled);
            enrollmentGender(gender, enrolled, not_enrolled);
            // age
            const agecounts = getCountsByAge(allEnrollments);
            const age = Object.keys(agecounts);
            const age_enrolled = age.map(a => agecounts[a].age_enrolled);
            const age_not_enrolled = age.map(a => agecounts[a].age_not_enrolled);
            enrollmentAge(age, age_enrolled, age_not_enrolled);

            // monthly enrollment
            const monthlycounts = getCountsByMonthly(allEnrollments);
            const monthly = Object.keys(monthlycounts);
            monthly.sort((a, b) => new Date(a) - new Date(b));
            const monthly_enrolled = monthly.map(a => monthlycounts[a].monthly_enrolled);
            enrollmentMonthly(monthly, monthly_enrolled);

            // laoding sequentially

            if (authenticating == 'Partner' || authenticating == 'County' || authenticating == 'Sub County') {

                // facilities enrollment
                const facilitycounts = getCountsByFacility(allEnrollments);
                const facility = Object.keys(facilitycounts);
                const facility_enrolled = facility.map(f => facilitycounts[f].enrolled);
                const facility_not_enrolled = facility.map(f => facilitycounts[f].not_enrolled);
                enrollmentFacility(facility, facility_enrolled, facility_not_enrolled);
            }

            if (authenticating == 'Admin' || authenticating == 'Donor') {
                // partner enrollment
                const partnercounts = getCountsByPartner(allEnrollments);
                const partner = Object.keys(partnercounts);
                const partner_enrolled = partner.map(p => partnercounts[p].enrolled);
                const partner_not_enrolled = partner.map(p => partnercounts[p].not_enrolled);
                enrollmentPartner(partner, partner_enrolled, partner_not_enrolled);
            }
            // module
            var accessCounts = {};
            data.all_module.forEach(function(item) {
                if (accessCounts[item.access]) {
                    accessCounts[item.access]++;
                } else {
                    accessCounts[item.access] = 1;
                }
            });
            var chartData = [];
            for (var access in accessCounts) {
                chartData.push({
                    name: access,
                    y: accessCounts[access]
                });
            }
            moduleChart(chartData);

            // Daily logins
            var dayOfWeekCounts = [0, 0, 0, 0, 0, 0, 0];
            var dayOfWeekDates = [
                [],
                [],
                [],
                [],
                [],
                [],
                []
            ];
            // Loop through the all_enrollment data to count login days
            allEnrollments.forEach(function(entry) {
                var loginDate = entry.last_login; // Assuming last_login is the date of login

                if (loginDate) {
                    var dayOfWeek = new Date(loginDate).getDay();
                    dayOfWeekCounts[dayOfWeek]++;
                    dayOfWeekDates[dayOfWeek].push(loginDate);
                }
            });

            // Create an array of day names for the x-axis labels
            var dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            // dailyLogin(dayNames, dayOfWeekCounts, dayOfWeekDates)

            // Initialize and render Highcharts column chart


            $("#client_enrolled").html(enrolledCount.toLocaleString());
            $("#client_profile").html(notenrolledCount.toLocaleString());
            $("#client").html(data.txcurr.toLocaleString());
            // $("#client").html(data.txcurr[0].tx_cur);
            $("#client_ushauri").html(totalCount.toLocaleString());


            $("#enrolledPercentage").text(enrolledPercentage.toFixed(1) + "%");
            $("#notEnrolledPercentage").text(notEnrolledPercentage.toFixed(1) + "%")

            if (authenticating == 'Facility') {
                var list_client = allEnrollments;
                $.each(list_client, function(index, item) {
                    $('#table_client tbody').append('<tr><td>' + item.upi_no + '</td><td>' + item.clinic_number + '</td><td>' + item.client_name + '</td><td>' + item.dob + '</td><td>' + item.phone_no + '</td><td>' + item.gender + '</td><td>' + item.enrollment + '</td><td>' + item.last_login + '</td></tr>');
                });
                $j('#table_client').DataTable({
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
            Swal.close();


        }
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
                "to": to
            },
            url: "{{ route('filter_nishauri_uptake') }}",
            success: function(data) {
                var allEnrollments = data.all_enrollment;
                console.log(allEnrollments);
                var enrolledCount = 0;
                var notenrolledCount = 0;

                if (authenticating == 'Admin' || authenticating == 'Donor' || authenticating == 'Partner' || authenticating == 'County' || authenticating == 'Sub County') {
                    function getCountsByGender(allEnrollments) {
                        const counts = {
                            Male: {
                                enrolled: 0,
                                not_enrolled: 0
                            },
                            Female: {
                                enrolled: 0,
                                not_enrolled: 0
                            },
                            Unknown: {
                                enrolled: 0,
                                not_enrolled: 0
                            }
                        };

                        data.all_enrollment.forEach(entry => {
                            const {
                                gender,
                                no_of_clients,
                                enrollment
                            } = entry;
                            if (enrollment === 'Enrolled') {
                                counts[gender].enrolled += no_of_clients;
                            } else if (enrollment === 'Not Enrolled') {
                                counts[gender].not_enrolled += no_of_clients;
                            }
                        });

                        return counts;
                    }

                    function getCountsByAge(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const age = entry.age_range;
                            if (!counts[age]) {
                                counts[age] = {
                                    age_enrolled: 0,
                                    age_not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[age].age_enrolled += entry.no_of_clients;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[age].age_not_enrolled += entry.no_of_clients;
                            }
                        });
                        return counts;
                    }

                    function getCountsByMonthly(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const monthly = entry.enrolled_month;
                            if (monthly !== null) {
                                if (!counts[monthly]) {
                                    counts[monthly] = {
                                        monthly_enrolled: 0,
                                        monthly_not_enrolled: 0
                                    };
                                }
                                if (entry.enrollment === 'Enrolled') {
                                    counts[monthly].monthly_enrolled += entry.no_of_clients;
                                } else if (entry.enrollment === 'Not Enrolled') {
                                    counts[monthly].monthly_not_enrolled += entry.no_of_clients;
                                }
                            }
                        });
                        return counts;
                    }
                    if (authenticating == 'Admin' || authenticating == 'Donor') {
                        function getCountsByPartner(data) {
                            const counts = {};
                            data.forEach(entry => {
                                const partner = entry.partner;
                                if (!counts[partner]) {
                                    counts[partner] = {
                                        enrolled: 0,
                                        not_enrolled: 0
                                    };
                                }
                                if (entry.enrollment === 'Enrolled') {
                                    counts[partner].enrolled += entry.no_of_clients;
                                } else if (entry.enrollment === 'Not Enrolled') {
                                    counts[partner].not_enrolled += entry.no_of_clients;
                                }
                            });
                            return counts;
                        }
                    } else {
                        function getCountsByFacility(data) {
                            const counts = {};
                            data.forEach(entry => {
                                const facility = entry.facility;
                                if (!counts[facility]) {
                                    counts[facility] = {
                                        enrolled: 0,
                                        not_enrolled: 0
                                    };
                                }
                                if (entry.enrollment === 'Enrolled') {
                                    counts[facility].enrolled += entry.no_of_clients;
                                } else if (entry.enrollment === 'Not Enrolled') {
                                    counts[facility].not_enrolled += entry.no_of_clients;
                                }
                            });
                            return counts;
                        }
                    }

                    for (let i = 0; i < allEnrollments.length; i++) {
                        if (allEnrollments[i].enrollment === "Enrolled") {
                            enrolledCount += allEnrollments[i].no_of_clients;
                        } else {
                            notenrolledCount += allEnrollments[i].no_of_clients;
                        }
                    }
                    var enrolledPercentage = 0;
                    var notEnrolledPercentage = 0;
                    var totalCount = enrolledCount + notenrolledCount;
                    if (totalCount !== 0) {
                        enrolledPercentage = (enrolledCount / totalCount) * 100;
                        notEnrolledPercentage = (notenrolledCount / totalCount) * 100;
                    }


                } else {

                    function getCountsByGender(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const gender = entry.gender;
                            if (!counts[gender]) {
                                counts[gender] = {
                                    enrolled: 0,
                                    not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[gender].enrolled++;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[gender].not_enrolled++;
                            }
                        });
                        return counts;
                    }

                    function getCountsByAge(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const age = entry.age_range;
                            if (!counts[age]) {
                                counts[age] = {
                                    age_enrolled: 0,
                                    age_not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[age].age_enrolled++;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[age].age_not_enrolled++;
                            }
                        });
                        return counts;
                    }

                    function getCountsByMonthly(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const monthly = entry.enrolled_month;
                            if (monthly !== null) {
                                if (!counts[monthly]) {
                                    counts[monthly] = {
                                        monthly_enrolled: 0,
                                    };
                                }
                                if (entry.enrollment === 'Enrolled') {
                                    counts[monthly].monthly_enrolled++;
                                }
                            }
                        });
                        return counts;
                    }

                    function getCountsByFacility(data) {
                        const counts = {};
                        data.forEach(entry => {
                            const facility = entry.facility;
                            if (!counts[facility]) {
                                counts[facility] = {
                                    enrolled: 0,
                                    not_enrolled: 0
                                };
                            }
                            if (entry.enrollment === 'Enrolled') {
                                counts[facility].enrolled++;
                            } else if (entry.enrollment === 'Not Enrolled') {
                                counts[facility].not_enrolled++;
                            }
                        });
                        return counts;
                    }


                    // function getCountsByPartner(data) {
                    //     const counts = {};
                    //     data.forEach(entry => {
                    //         const partner = entry.partner;
                    //         if (!counts[partner]) {
                    //             counts[partner] = {
                    //                 enrolled: 0,
                    //                 not_enrolled: 0
                    //             };
                    //         }
                    //         if (entry.enrollment === 'Enrolled') {
                    //             counts[partner].enrolled++;
                    //         } else if (entry.enrollment === 'Not Enrolled') {
                    //             counts[partner].not_enrolled++;
                    //         }
                    //     });
                    //     return counts;
                    // }

                    for (var i = 0; i < allEnrollments.length; i++) {
                        if (allEnrollments[i].enrollment === "Enrolled") {
                            enrolledCount++;
                        } else {
                            notenrolledCount++;
                        }
                    }

                    var enrolledPercentage = 0;
                    var notEnrolledPercentage = 0;
                    var totalCount = enrolledCount + notenrolledCount;
                    if (totalCount !== 0) {
                        enrolledPercentage = (enrolledCount / totalCount) * 100;
                        notEnrolledPercentage = (notenrolledCount / totalCount) * 100;
                    }

                }

                // gender
                const counts = getCountsByGender(allEnrollments);
                const gender = Object.keys(counts);
                const enrolled = gender.map(g => counts[g].enrolled);
                const not_enrolled = gender.map(g => counts[g].not_enrolled);
                enrollmentGender(gender, enrolled, not_enrolled);
                // age
                const agecounts = getCountsByAge(allEnrollments);
                const age = Object.keys(agecounts);
                const age_enrolled = age.map(a => agecounts[a].age_enrolled);
                const age_not_enrolled = age.map(a => agecounts[a].age_not_enrolled);
                enrollmentAge(age, age_enrolled, age_not_enrolled);

                // monthly enrollment
                const monthlycounts = getCountsByMonthly(allEnrollments);
                const monthly = Object.keys(monthlycounts);
                monthly.sort((a, b) => new Date(a) - new Date(b));
                const monthly_enrolled = monthly.map(a => monthlycounts[a].monthly_enrolled);
                enrollmentMonthly(monthly, monthly_enrolled);

                // laoding sequentially

                if (authenticating == 'Partner' || authenticating == 'County' || authenticating == 'Sub County') {

                    // facilities enrollment
                    const facilitycounts = getCountsByFacility(allEnrollments);
                    const facility = Object.keys(facilitycounts);
                    const facility_enrolled = facility.map(f => facilitycounts[f].enrolled);
                    const facility_not_enrolled = facility.map(f => facilitycounts[f].not_enrolled);
                    enrollmentFacility(facility, facility_enrolled, facility_not_enrolled);
                }

                if (authenticating == 'Admin' || authenticating == 'Donor') {
                    // partner enrollment
                    const partnercounts = getCountsByPartner(allEnrollments);
                    const partner = Object.keys(partnercounts);
                    const partner_enrolled = partner.map(p => partnercounts[p].enrolled);
                    const partner_not_enrolled = partner.map(p => partnercounts[p].not_enrolled);
                    enrollmentPartner(partner, partner_enrolled, partner_not_enrolled);
                }
                // module
                var accessCounts = {};
                data.all_module.forEach(function(item) {
                    if (accessCounts[item.access]) {
                        accessCounts[item.access]++;
                    } else {
                        accessCounts[item.access] = 1;
                    }
                });
                var chartData = [];
                for (var access in accessCounts) {
                    chartData.push({
                        name: access,
                        y: accessCounts[access]
                    });
                }
                moduleChart(chartData);

                // Daily logins
                var dayOfWeekCounts = [0, 0, 0, 0, 0, 0, 0];
                var dayOfWeekDates = [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    []
                ];
                // Loop through the all_enrollment data to count login days
                allEnrollments.forEach(function(entry) {
                    var loginDate = entry.last_login; // Assuming last_login is the date of login

                    if (loginDate) {
                        var dayOfWeek = new Date(loginDate).getDay();
                        dayOfWeekCounts[dayOfWeek]++;
                        dayOfWeekDates[dayOfWeek].push(loginDate);
                    }
                });

                // Create an array of day names for the x-axis labels
                var dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                // dailyLogin(dayNames, dayOfWeekCounts, dayOfWeekDates)

                // Initialize and render Highcharts column chart


                $("#client_enrolled").html(enrolledCount.toLocaleString());
                $("#client_profile").html(notenrolledCount.toLocaleString());
                $("#client").html(data.txcurr.toLocaleString());
                // $("#client").html(data.txcurr[0].tx_cur);
                $("#client_ushauri").html(totalCount.toLocaleString());


                $("#enrolledPercentage").text(enrolledPercentage.toFixed(1) + "%");
                $("#notEnrolledPercentage").text(notEnrolledPercentage.toFixed(1) + "%")

                if (authenticating == 'Facility') {
                    var list_client = allEnrollments;
                    var table = $j('#table_client').DataTable();

                    // Destroy the DataTable instance
                    table.destroy();

                    $('#table_client tbody').empty();
                    $.each(list_client, function(index, item) {
                        $('#table_client tbody').append('<tr><td>' + item.upi_no + '</td><td>' + item.clinic_number + '</td><td>' + item.client_name + '</td><td>' + item.dob + '</td><td>' + item.phone_no + '</td><td>' + item.gender + '</td><td>' + item.enrollment + '</td><td>' + item.last_login + '</td></tr>');
                    });
                    $j('#table_client').DataTable({
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
                Swal.close();


            }
        });
    });

    function isWithinLastCalendarWeek(dateString) {
        var today = new Date();
        var date = new Date(dateString);
        var lastWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);
        return date >= lastWeek && date <= today;
    }

    function enrollmentGender(gender, enrolled, not_enrolled) {
        Highcharts.chart('enrollment_gender', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },
            title: {
                text: 'Enrollment Status by Gender',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },

            xAxis: {
                categories: gender,
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Patient'
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
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Enrolled',
                color: '#01058A',
                data: enrolled
            }, {
                name: 'Not Enrolled',
                color: '#97080F',
                data: not_enrolled

            }]
        });
    }

    function enrollmentAge(age, age_enrolled, age_not_enrolled) {
        Highcharts.chart('enrollment_age', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },
            title: {
                text: 'Enrollment Status by Age Group',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },

            xAxis: {
                categories: age,
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Patient'
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
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Enrolled',
                color: '#01058A',
                data: age_enrolled
            }, {
                name: 'Not Enrolled',
                color: '#97080F',
                data: age_not_enrolled

            }]
        });
    }

    function enrollmentMonthly(monthly, monthly_enrolled) {
        Highcharts.chart('enrollment_monthly', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },
            title: {
                text: 'Monthly Enrollments',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },

            xAxis: {
                categories: monthly,
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Patient'
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
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Enrolled',
                color: '#01058A',
                data: monthly_enrolled
            }]
        });
    }

    // function dailyLogin(dayNames, dayOfWeekCounts, dayOfWeekDates) {
    //     Highcharts.chart('daily_login', {
    //         chart: {
    //             type: 'column'
    //         },
    //         title: {
    //             text: 'Login Counts by Day of the Week',
    //             style: {
    //                 fontFamily: 'Inter',
    //                 fontSize: '14px'
    //             }
    //         },
    //         style: {
    //             fontFamily: 'Inter',
    //             fontSize: '14px'
    //         },
    //         xAxis: {
    //             categories: dayNames,
    //             title: {
    //                 text: 'Day of the Week'
    //             },
    //             style: {
    //                 fontFamily: 'Inter',
    //                 fontSize: '14px'
    //             }
    //         },
    //         yAxis: {
    //             title: {
    //                 text: 'Number of Patients'
    //             }
    //         },
    //         tooltip: {
    //             formatter: function() {
    //                 var dayIndex = this.point.index;
    //                 var loginDates = dayOfWeekDates[dayIndex].map(date => Highcharts.dateFormat('%Y-%m-%d', new Date(date)));
    //                 return '<b>' + this.x + '</b><br>Logins: ' + this.y + '<br>Date: ' + loginDates.join(', ');
    //             }
    //         },
    //         series: [{
    //             name: 'Logins',
    //             color: '#01058A',
    //             data: dayOfWeekCounts
    //         }]
    //     });
    // }

    function moduleChart(chartData) {
        Highcharts.chart('module_access', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },
            title: {
                text: 'Modules Access Uptake',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: chartData.map(item => item.name),
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            yAxis: {
                title: {
                    text: 'No of Patients'
                }
            },
            series: [{
                name: 'Modules',
                color: '#01058A',
                data: chartData.map(item => item.y),
            }]
        });
    }
    if (authenticating == 'Partner' || authenticating == 'County' || authenticating == 'Sub County') {

        function enrollmentFacility(facility, facility_enrolled, facility_not_enrolled) {
            Highcharts.chart('enrollment_facility', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                style: {
                    fontFamily: 'Inter'
                },
                title: {
                    text: 'Enrollment Status by Facilities',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },

                xAxis: {
                    categories: facility,
                    crosshair: true
                },
                yAxis: {
                    title: {
                        useHTML: true,
                        text: 'Patients'
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
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Enrolled',
                    color: '#01058A',
                    data: facility_enrolled
                }, {
                    name: 'Not Enrolled',
                    color: '#97080F',
                    data: facility_not_enrolled

                }]
            });
        }
    }
    if (authenticating == 'Admin' || authenticating == 'Donor') {
        function enrollmentPartner(partner, partner_enrolled, partner_not_enrolled) {
            Highcharts.chart('enrollment_partner', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                style: {
                    fontFamily: 'Inter'
                },
                title: {
                    text: 'Enrollment Status by Partners',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },

                xAxis: {
                    categories: partner,
                    crosshair: true
                },
                yAxis: {
                    title: {
                        useHTML: true,
                        text: 'Patients'
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
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Enrolled',
                    color: '#01058A',
                    data: partner_enrolled
                }, {
                    name: 'Not Enrolled',
                    color: '#97080F',
                    data: partner_not_enrolled

                }]
            });
        }
    }
</script>





<!-- end of col -->

@endsection