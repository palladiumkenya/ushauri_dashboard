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
@if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')


<div class="col">

<form role="form" method="get" action="{{route('filter_charts')}}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
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
            <div class="col">
                <div class="form-group">
                    <select class="form-control county  input-rounded input-sm select2" id="counties" name="county">
                        <option value="">County:</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value=""> Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">.</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>

                    <select class="form-control filter_facility input-rounded input-sm select2">
                        <option value="">Module : </option>
                        <option value="">DSD</option>
                        <option value="">PMTCT</option>
                    </select>
                </div>
            </div>

            <div class='col'>
                                <div class="form-group">
                                    <div class="input-group">

                                        <div class="col-md-10">

                                            <input type="date" id="from" class="form-control" placeholder="From" name="from">
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="button">
                                                <i class="icon-regular i-Calendar-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
            <div class="col">
            <div class='col'>
                                <div class="form-group">
                                    <div class="input-group">

                                        <div class="col-md-10">

                                            <input type="date" id="to" class="form-control" placeholder="To" name="to">
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="button">
                                                <i class="icon-regular i-Calendar-4"></i>
                                            </button>
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
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">Dashboard</a>
        <a class="nav-item nav-link" id="nav-client-tab" data-toggle="tab" href="#nav-client" role="tab" aria-controls="nav-client" aria-selected="false">Clients</a>
        <a class="nav-item nav-link" id="nav-appointment-tab" data-toggle="tab" href="#nav-appointment" role="tab" aria-controls="nav-appointment" aria-selected="false">Appointments</a>
        <a class="nav-item nav-link" id="nav-missed-tab" data-toggle="tab" href="#nav-missed" role="tab" aria-controls="nav-missed" aria-selected="false">Missed Appointnments</a>
        <a class="nav-item nav-link"  data-toggle="tab" href="{{route('new_client')}}" role="tab"  aria-selected="false">Indicators Definitions</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">


        <div id="highchart"></div>
        <div class="row">

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Facilities </p>

                            <p class="text-primary text-20 line-height-1 mb-1">{{count($active_facilities)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Clients</p>

                            <p id="allApps" class="text-primary text-20 line-height-1 mb-2">{{$client}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Appointments</p>

                            <p id="keptApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Missed Appointments</p>

                            <p id="defaultedApps" class="text-primary text-20 line-height-1 mb-2">{{$missed_appointment}}</p>
                        </div>
                    </div>
                </div>
            </div>


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
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="total_missed_appointment_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="total_missed_appointment_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>

    </div>
    <!-- main dashbaord ends -->

    <!-- client dashbaord starts -->
    <div class="tab-pane fade" id="nav-client" role="tabpanel" aria-labelledby="nav-client-tab">
        <div class="row">
            <div class="col-lg-4 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Clients</p>

                            <p id="allApps" class="text-primary text-20 line-height-1 mb-2">{{$client}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Consented</p>

                            <p id="keptApps" class="text-primary text-20 line-height-1 mb-2">{{$client_consented}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Non Consented</p>

                            <p id="defaultedApps" class="text-primary text-20 line-height-1 mb-2">{{$client_nonconsented}}</p>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="consented_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="consented_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="nonconsented_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="nonconsented_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>
    </div>

    <!-- client dashboard ends -->
    <div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">
        <div class="row">
            <div class="col-lg-4 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Appointments</p>

                            <p id="allApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Honored</p>

                            <p id="keptApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_honoured}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Not Honored</p>

                            <p id="defaultedApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_not_honoured}}</p>
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
    <div class="tab-pane fade" id="nav-missed" role="tabpanel" aria-labelledby="nav-missed-tab">
    <div class="row">
            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Total Missed</p>

                            <p id="allApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_not_honoured}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Missed</p>

                            <p id="keptApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_missed}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Defaulted</p>

                            <p id="defaultedApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_defaulted}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Lost To Follow Up</p>

                            <p id="defaultedApps" class="text-primary text-20 line-height-1 mb-2">{{$appointment_lftu}}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_missed_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_missed_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_defaulted_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_defaulted_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card-body row">
                    <div id="appointment_ltfu_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">
                <div class="card-body row">
                    <div id="appointment_lftu_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>
    </div>
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


<script type="text/javascript">
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

    var Appointment_male = <?php echo json_encode($appointment_male) ?>;
    var Appointment_female = <?php echo json_encode($appointment_female) ?>;
    var Appointment_uknown_gender = <?php echo json_encode($appointment_uknown_gender) ?>;
    var Appointment_to_nine = <?php echo json_encode($appointment_to_nine) ?>;
    var Appointment_to_fourteen = <?php echo json_encode($appointment_to_fourteen) ?>;
    var Appointment_to_nineteen = <?php echo json_encode($appointment_to_nineteen) ?>;
    var Appointment_to_twentyfour = <?php echo json_encode($appointment_to_twentyfour) ?>;
    var Appointment_to_twentyfive_above = <?php echo json_encode($appointment_to_twentyfive_above) ?>;
    var Appointment_uknown_age = <?php echo json_encode($appointment_uknown_age) ?>;

    var Appointment_total_missed_female = <?php echo json_encode($appointment_total_missed_female) ?>;
    var Appointment_total_missed_male = <?php echo json_encode($appointment_total_missed_male) ?>;
    var Appointment_total_missed_uknown_gender = <?php echo json_encode($appointment_total_missed_uknown_gender) ?>;
    var Appointment_total_missed_to_nine = <?php echo json_encode($appointment_total_missed_to_nine) ?>;
    var Appointment_total_missed_to_fourteen = <?php echo json_encode($appointment_total_missed_to_fourteen) ?>;
    var Appointment_total_missed_to_nineteen = <?php echo json_encode($appointment_total_missed_to_nineteen) ?>;
    var Appointment_total_missed_to_twentyfour = <?php echo json_encode($appointment_total_missed_to_twentyfour) ?>;
    var Appointment_total_missed_to_twentyfive_above = <?php echo json_encode($appointment_total_missed_to_twentyfive_above) ?>;
    var Appointment_total_missed_uknown_age = <?php echo json_encode($appointment_total_missed_uknown_age) ?>;

    var Client_consented_male = <?php echo json_encode($client_consented_male) ?>;
    var Client_consented_female = <?php echo json_encode($client_consented_female) ?>;
    var Client_consented_uknown_gender = <?php echo json_encode($client_consented_uknown_gender) ?>;
    var Client_nonconsented_male = <?php echo json_encode($client_nonconsented_male) ?>;
    var Client_nonconsented_female = <?php echo json_encode($client_nonconsented_female) ?>;
    var Client_nonconsented_uknown_gender = <?php echo json_encode($client_nonconsented_uknown_gender) ?>;
    var Client_consented_to_nine = <?php echo json_encode($client_consented_to_nine) ?>;
    var Client_consented_to_fourteen = <?php echo json_encode($client_consented_to_fourteen) ?>;
    var Client_consented_to_nineteen = <?php echo json_encode($client_consented_to_nineteen) ?>;
    var Client_consented_to_twentyfour = <?php echo json_encode($client_consented_to_twentyfour) ?>;
    var Client_consented_to_twentyfive_above = <?php echo json_encode($client_consented_to_twentyfive_above) ?>;
    var Client_consented_uknown_age = <?php echo json_encode($client_consented_uknown_age) ?>;
    var Client_nonconsented_to_nine = <?php echo json_encode($client_nonconsented_to_nine) ?>;
    var Client_nonconsented_to_fourteen = <?php echo json_encode($client_nonconsented_to_fourteen) ?>;
    var Client_nonconsented_to_nineteen = <?php echo json_encode($client_nonconsented_to_nineteen) ?>;
    var Client_nonconsented_to_twentyfour = <?php echo json_encode($client_nonconsented_to_twentyfour) ?>;
    var Client_nonconsented_to_twentyfive_above = <?php echo json_encode($client_nonconsented_to_twentyfive_above) ?>;
    var Client_nonconsented_uknown_age = <?php echo json_encode($client_nonconsented_uknown_age) ?>;


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


    var Appointment_missed_male = <?php echo json_encode($appointment_missed_male) ?>;
    var Appointment_missed_female = <?php echo json_encode($appointment_missed_female) ?>;
    var Appointment_missed_uknown_gender = <?php echo json_encode($appointment_missed_uknown_gender) ?>;
    var Appointment_missed_to_nine = <?php echo json_encode($appointment_missed_to_nine) ?>;
    var Appointment_missed_to_fourteen = <?php echo json_encode($appointment_missed_to_fourteen) ?>;
    var Appointment_missed_to_nineteen = <?php echo json_encode($appointment_missed_to_nineteen) ?>;
    var Appointment_missed_to_twentyfour = <?php echo json_encode($appointment_missed_to_twentyfour) ?>;
    var Appointment_missed_to_twentyfive_above = <?php echo json_encode($appointment_missed_to_twentyfive_above) ?>;
    var Appointment_missed_to_uknown_age = <?php echo json_encode($appointment_missed_to_uknown_age) ?>;
    var Appointment_defaulted_female = <?php echo json_encode($appointment_defaulted_female) ?>;
    var Appointment_defaulted_male = <?php echo json_encode($appointment_defaulted_male) ?>;
    var Appointment_defaulted_uknown_gender = <?php echo json_encode($appointment_defaulted_uknown_gender) ?>;
    var Appointment_defaulted_to_nine = <?php echo json_encode($appointment_defaulted_to_nine) ?>;
    var Appointment_defaulted_to_fourteen = <?php echo json_encode($appointment_defaulted_to_fourteen) ?>;
    var Appointment_defaulted_to_nineteen = <?php echo json_encode($appointment_defaulted_to_nineteen) ?>;
    var Appointment_defaulted_to_twentyfour = <?php echo json_encode($appointment_defaulted_to_twentyfour) ?>;
    var Appointment_defaulted_to_twentyfive_above = <?php echo json_encode($appointment_defaulted_to_twentyfive_above) ?>;
    var Appointment_defaulted_to_uknown_age = <?php echo json_encode($appointment_defaulted_to_uknown_age) ?>;
    var Appointment_ltfu_female = <?php echo json_encode($appointment_ltfu_female) ?>;
    var Appointment_ltfu_male = <?php echo json_encode($appointment_ltfu_male) ?>;
    var Appointment_ltfu_uknown_gender = <?php echo json_encode($appointment_ltfu_uknown_gender) ?>;
    var Appointment_ltfu_to_nine = <?php echo json_encode($appointment_ltfu_to_nine) ?>;
    var Appointment_ltfu_to_fourteen = <?php echo json_encode($appointment_ltfu_to_fourteen) ?>;
    var Appointment_ltfu_to_nineteen = <?php echo json_encode($appointment_ltfu_to_nineteen) ?>;
    var Appointment_ltfu_to_twentyfour = <?php echo json_encode($appointment_ltfu_to_twentyfour) ?>;
    var Appointment_ltfu_to_twentyfive_above = <?php echo json_encode($appointment_ltfu_to_twentyfive_above) ?>;
    var Appointment_ltfu_to_uknown_age = <?php echo json_encode($appointment_ltfu_to_uknown_age) ?>;


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
                "facilities": facilities
            },
            url: "{{ route('filter_appointment_dashboard') }}",
            success: function(data) {


            }
        });
    });



    var appChart = Highcharts.chart('client_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Client Registration By Gender'
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

    var appChart = Highcharts.chart('client_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Client Registration By Age'
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

    var appChart = Highcharts.chart('appointment_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Appointments'
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
            data: [Appointment_male, Appointment_female, Appointment_uknown_gender]
        }],

    });

    var appChart = Highcharts.chart('appointment_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Appointments'
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
            data: [Appointment_to_nine, Appointment_to_fourteen, Appointment_to_nineteen, Appointment_to_twentyfour, Appointment_to_twentyfive_above, Appointment_uknown_age]
        }],

    });

    // missed appointment charts
    var appChart = Highcharts.chart('total_missed_appointment_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total Missed Appointment By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Missed Appointments'
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
            data: [Appointment_total_missed_male, Appointment_total_missed_female, Appointment_total_missed_uknown_gender]
        }],

    });

    var appChart = Highcharts.chart('total_missed_appointment_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total Missed Appointment By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Missed Appointments'
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
            data: [Appointment_total_missed_to_nine, Appointment_total_missed_to_fourteen, Appointment_total_missed_to_nineteen, Appointment_total_missed_to_twentyfour, Appointment_total_missed_to_twentyfive_above, Appointment_total_missed_uknown_age]
        }],

    });

    // CONSENTED CLIENTS GENDER
    var appChart = Highcharts.chart('consented_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Consented Clients By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Consented Clients'
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
            data: [Client_consented_male, Client_consented_female, Client_consented_uknown_gender]
        }],

    });
    // CONSENTED CLIENTS AGE
    var appChart = Highcharts.chart('consented_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Consented Clients By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Consented Clients'
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
            data: [Client_consented_to_nine, Client_consented_to_fourteen, Client_consented_to_nineteen, Client_consented_to_twentyfour, Client_consented_to_twentyfive_above, Client_consented_uknown_age]
        }],

    });

    //NON CONSENTED CLIENTS GENDER
    var appChart = Highcharts.chart('nonconsented_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Non Consented Clients By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Non Consented Clients'
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
            data: [Client_nonconsented_male, Client_nonconsented_female, Client_nonconsented_uknown_gender]
        }],

    });
// NON CONSENTED AGE
    var appChart = Highcharts.chart('nonconsented_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Non Consented Clients By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Non Consented Clients'
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
            data: [Client_nonconsented_to_nine, Client_nonconsented_to_fourteen, Client_nonconsented_to_nineteen, Client_nonconsented_to_twentyfour, Client_nonconsented_to_twentyfive_above, Client_nonconsented_uknown_age]
        }],

    });

    //APPOINTMENT HONOURED GENDER
    var appChart = Highcharts.chart('appointment_honoured_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Honoured By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Honoured'
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
    var appChart = Highcharts.chart('appointment_honoured_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Honoured By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Honoured'
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
    var appChart = Highcharts.chart('appointment_not_honoured_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Not Honoured By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Not Honoured'
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
    var appChart = Highcharts.chart('appointment_not_honoured_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Not Honoured By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Not Honoured'
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

    //MISSED APPOINTMENT BY GENDER
    var appChart = Highcharts.chart('appointment_missed_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Missed By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Missed'
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
            data: [Appointment_missed_male, Appointment_missed_female, Appointment_missed_uknown_gender]
        }],

    });
// APPOINTMENT MISSED AGE
    var appChart = Highcharts.chart('appointment_missed_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Missed By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Missed'
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
            data: [Appointment_missed_to_nine, Appointment_missed_to_fourteen, Appointment_missed_to_nineteen, Appointment_missed_to_twentyfour, Appointment_missed_to_twentyfive_above, Appointment_missed_to_uknown_age]
        }],

    });
    //DEFAULTED APPOINTMENT BY GENDER
    var appChart = Highcharts.chart('appointment_defaulted_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Defaulted By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Defaulted'
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
            data: [Appointment_defaulted_male, Appointment_defaulted_female, Appointment_defaulted_uknown_gender]
        }],

    });
// APPOINTMENT DEFAULTED AGE
    var appChart = Highcharts.chart('appointment_defaulted_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Defaulted By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment Defaulted'
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
            data: [Appointment_defaulted_to_nine, Appointment_defaulted_to_fourteen, Appointment_defaulted_to_nineteen, Appointment_defaulted_to_twentyfour, Appointment_defaulted_to_twentyfive_above, Appointment_defaulted_to_uknown_age]
        }],

    });

      //LTFU APPOINTMENT BY GENDER
      var appChart = Highcharts.chart('appointment_ltfu_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment LTFU By Gender'
        },
        xAxis: {
            categories: ['Male', 'Female', 'UKNOWN Gender']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment LTFU'
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
            data: [Appointment_ltfu_male, Appointment_ltfu_female, Appointment_ltfu_uknown_gender]
        }],

    });
// APPOINTMENT LTFU AGE
    var appChart = Highcharts.chart('appointment_lftu_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment LTFU By Age'
        },
        xAxis: {
            categories: ['0-9 YRS', '10-14 YRS', '15-19 YRS', '20-24 YRS', '25+ YRS', 'UKNOWN AGE']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of Appointment LTFU'
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
            data: [Appointment_ltfu_to_nine, Appointment_ltfu_to_fourteen, Appointment_ltfu_to_nineteen, Appointment_ltfu_to_twentyfour, Appointment_ltfu_to_twentyfive_above, Appointment_ltfu_to_uknown_age]
        }],

    });


    var colors = Highcharts.getOptions().colors;
</script>





<!-- end of col -->

@endsection