@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

<style rel="stylesheet" type="text/css">
    .tab-content {
        font-family: 'Manrope';
        font-style: normal;
        font-weight: 500;
        font-size: 12px;
        line-height: 16px;
    }

    .TX_Curr {

        background: #369FFF;
        box-shadow: 0px 10px 30px rgba(255, 153, 58, 0.4);
        border-radius: 10px;

    }

    .Booked {

        background: #8AC53E;
        border: 1px solid #E5F7FF;
        border-radius: 10px;

    }

    .Consented {

        background: #FF993A;
        box-shadow: 0px 10px 30px rgba(184, 146, 222, 0.4);
        border-radius: 10px;

    }

    .Messages {

        background: #663399;
        box-shadow: 0px 10px 30px rgba(138, 197, 62, 0.4);
        border-radius: 10px;

    }

    .Kept {

        background: #369FFF;
        box-shadow: 0px 10px 30px rgba(255, 153, 58, 0.4);
        border-radius: 10px;

    }

    .Future {

        background: #8AC53E;
        border: 1px solid #E5F7FF;
        border-radius: 10px;

    }

    .Not_Kept {

        background: #FF993A;
        box-shadow: 0px 10px 30px rgba(184, 146, 222, 0.4);
        border-radius: 10px;

    }

    .Indicator {
        position: absolute;
        width: 207px;
        height: 24px;
        left: 243.5px;
        top: 878px;

        font-family: 'Manrope';
        font-style: normal;
        font-weight: 700;
        font-size: 17px;
        line-height: 140%;
        /* identical to box height, or 24px */
        letter-spacing: -0.3px;
        text-transform: uppercase;


    }
</style>
@endsection

@section('main-content')
<!-- <div class="breadcrumb">
                <ul>
                    <li><a href="">Appointment Dashboard</a></li>
                    <li></li>
                </ul>
            </div> -->

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">Appointment trends</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-appointment" role="tab" aria-selected="false">Missed Appointments</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-3">
                    <div class="TX_Curr card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span>{{ number_format(json_decode($all_tx_curr[0]->tx_cur)) }}</span>
                                <p>TX_Curr</p>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="Consented card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>{{ number_format(json_decode($consented_clients[0]->consented)) }}</span>
                                <p>Clients Consented</p>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="Booked card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>{{ number_format(json_decode($all_appoinments[0]->total_app)) }}</span>
                                <p>Booked Appointments</p>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="Messages card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span>{{ number_format(json_decode($all_appoinments[0]->messages)) }}</span>
                                <p>Received Messages</p>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-4">
                    <div class="Kept card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span class="">{{ number_format(json_decode($all_appoinments[0]->kept_app)) }}</span>
                                <p class="pt-0">Appointments Kept</p>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Not_Kept card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span class="">{{ number_format(json_decode($all_appoinments[0]->not_kept_app)) }}</span>
                                <p class="pt-0">Appointments Not Kept</p>
                                <span></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Future card  o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>{{ number_format(json_decode($all_appoinments[0]->future)) }}</span>
                                <p>Future Appointments</p>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class=" card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <p class="line-height-1 mb-2"></p>
                                <p class="Indicator">Indicator Definition</p>

                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-6">
                    <div class="card-body row">
                        <div id="appointment_gender" class="appointment_gender_chart" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
                <div class="col-6">

                    <div class="card-body row">
                        <div id="appointment_age" class="col" style="height:  400px;margin-top:20px;width: 900px;"></div> <br />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_marital" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @if (Auth::user()->access_level == 'Admin')
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_county" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>

        @endif

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Facility')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>
                            <div class="table-responsive">
                                <table id="verification_table_client" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>NUPI No</th>
                                            <th>Clinic No</th>
                                            <th>Client Name</th>
                                            <th>DOB</th>
                                            <th>Phone No</th>
                                            <th>SMS Consent</th>
                                            <th>Status</th>
                                            <th>Phone No</th>
                                            <th>Appointment Kept</th>
                                            <th>Appointment Not Kept</th>
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
    <!-- appointment tab -->
    <div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">


        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-4">
                    <div class="TX_Curr card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span>250,000</span>
                                <p>Clients With Missed Appointment</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Consented card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>150,000</span>
                                <p>Consented Clients Who Missed</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Booked card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>100,000</span>
                                <p>Clients Who Received SMS</p>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-4">
                    <div class="Kept card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <span>30,000</span>
                                <p>Clients Called</p>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Not_Kept card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>20,000</span>
                                <p>Clients Physically Traced</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="Future card o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <span>25,000</span>
                                <p>Clients Who RTC</p>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class=" card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                        <div class="card-body">
                            <div class="content">
                                <p class="line-height-1 mb-2"></p>
                                <p class="Indicator">Indicator Definition</p>

                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-6">
                    <div class="card-body row">
                        <div id="missed_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
                <div class="col-6">

                    <div class="card-body row">
                        <div id="missed_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_marital" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Facility')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>
                            <div class="table-responsive">
                                <table id="verification_table_client" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>NUPI No</th>
                                            <th>Clinic No</th>
                                            <th>Client Name</th>
                                            <th>DOB</th>
                                            <th>Phone No</th>
                                            <th>SMS Consent</th>
                                            <th>Status</th>
                                            <th>Phone No</th>
                                            <th>Appointment Kept</th>
                                            <th>Appointment Not Kept</th>
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

</div>






<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/css/highcharts.css"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>


<script type="text/javascript">


    var All_Appointments = <?php echo json_decode($all_appoinments[0]->kept_app) ?>;

    console.log(All_Appointments);

    // kept vs not kept gender
    // var Appointment_Kept_Male = Appointment_Gender.findIndex(item => item.Gender === 'M');
    // var Final_Appointment_Kept_Male = (Appointment_Gender[Appointment_Kept_Male].Percent_Kept * 1) //male kept
    // var Appointment_Not_Kept_Male = Appointment_Gender.findIndex(item => item.Gender === 'M');
    // var Final_Appointment_Not_Kept_Male = (Appointment_Gender[Appointment_Not_Kept_Male].Percent_Not_Kept * 1) //male not kept
    // var Appointment_Kept_Female = Appointment_Gender.findIndex(item => item.Gender === 'F');
    // var Final_Appointment_Kept_Female = (Appointment_Gender[Appointment_Kept_Female].Percent_Kept * 1) //male kept
    // var Appointment_Not_Kept_Female = Appointment_Gender.findIndex(item => item.Gender === 'F');
    // var Final_Appointment_Not_Kept_Female = (Appointment_Gender[Appointment_Not_Kept_Female].Percent_Not_Kept * 1) //male not kept

    // kept age
    // var App_Kept_To_Nine = Appointment_Age.findIndex(item => item.Age === '0-9');
    // var Final_App_Kept_To_Nine = (Appointment_Age[App_Kept_To_Nine].percent_Kept) //nine kept
    // console.log(Final_App_Kept_To_Nine);

    // var App_Kept_To_Fourteen = Appointment_Age.findIndex(item => item.Age === '10-14');
    // var Final_App_Kept_To_Fourteen = (Appointment_Age[App_Kept_To_Fourteen].percent_Kept) //fourteen kept
    // var App_Kept_To_Nineteen = Appointment_Age.findIndex(item => item.Age === '15-19');
    // var Final_App_Kept_To_Nineteen = (Appointment_Age[App_Kept_To_Nineteen].percent_Kept) //nineteen kept
    // var App_Kept_To_TwentyFour = Appointment_Age.findIndex(item => item.Age === '20-24');
    // var Final_App_Kept_To_TwentyFour = (Appointment_Age[App_Kept_To_TwentyFour].percent_Kept) //TwentyFour kept
    // var App_Kept_To_TwentyNine = Appointment_Age.findIndex(item => item.Age === '25-29');
    // var Final_App_Kept_To_TwentyNine = (Appointment_Age[App_Kept_To_TwentyNine].percent_Kept) //TwentyNine kept
    // var App_Kept_To_ThirtyFour = Appointment_Age.findIndex(item => item.Age === '30-34');
    // var Final_App_Kept_To_ThirtyFour = (Appointment_Age[App_Kept_To_ThirtyFour].percent_Kept) //ThirtyFour kept
    // var App_Kept_To_ThirtyNine = Appointment_Age.findIndex(item => item.Age === '35-39');
    // var Final_App_Kept_To_ThirtyNine = (Appointment_Age[App_Kept_To_ThirtyNine].percent_Kept) //ThirtyNine kept
    // var App_Kept_To_FortyFour = Appointment_Age.findIndex(item => item.Age === '40-44');
    // var Final_App_Kept_To_FortyFour = (Appointment_Age[App_Kept_To_FortyFour].percent_Kept) //FortyFour kept
    // var App_Kept_To_FortyNine = Appointment_Age.findIndex(item => item.Age === '45-49');
    // var Final_App_Kept_To_FortyNine = (Appointment_Age[App_Kept_To_FortyNine].percent_Kept) //FortyNine kept
    // var App_Kept_To_FiftyFour = Appointment_Age.findIndex(item => item.Age === '50-54');
    // var Final_App_Kept_To_FiftyFour = (Appointment_Age[App_Kept_To_FiftyFour].percent_Kept) //FiftyFour kept
    // var App_Kept_To_FiftyNine = Appointment_Age.findIndex(item => item.Age === '55-59');
    // var Final_App_Kept_To_FiftyNine = (Appointment_Age[App_Kept_To_FiftyNine].percent_Kept) //FiftyNine kept
    // var App_Kept_To_SixtyFour = Appointment_Age.findIndex(item => item.Age === '60-64');
    // var Final_App_Kept_To_SixtyFour = (Appointment_Age[App_Kept_To_SixtyFour].percent_Kept) //SixtyFour kept
    // var App_Kept_To_SixtyFivePlus = Appointment_Age.findIndex(item => item.Age === '65+');
    // var Final_App_Kept_To_SixtyFivePlus = (Appointment_Age[App_Kept_To_SixtyFivePlus].percent_Kept) //SixtyFivePlus kept

    // // not kept age
    // var App_Not_Kept_To_Nine = Appointment_Age.findIndex(item => item.Age === '0-9');
    // var Final_App_Not_Kept_To_Nine = (Appointment_Age[App_Not_Kept_To_Nine].percent_not_kept) //nine not kept
    // var App_Not_Kept_To_Fourteen = Appointment_Age.findIndex(item => item.Age === '10-14');
    // var Final_Not_App_Kept_To_Fourteen = (Appointment_Age[App_Not_Kept_To_Fourteen].percent_not_kept) //fourteen not kept
    // var App_Not_Kept_To_Nineteen = Appointment_Age.findIndex(item => item.Age === '15-19');
    // var Final_Not_App_Kept_To_Nineteen = (Appointment_Age[App_Not_Kept_To_Nineteen].percent_not_kept) //nineteen not kept
    // var App_Not_Kept_To_TwentyFour = Appointment_Age.findIndex(item => item.Age === '20-24');
    // var Final_Not_App_Kept_To_TwentyFour = (Appointment_Age[App_Not_Kept_To_TwentyFour].percent_not_kept) //TwentyFour not kept
    // var App_Not_Kept_To_TwentyNine = Appointment_Age.findIndex(item => item.Age === '25-29');
    // var Final_Not_App_Kept_To_TwentyNine = (Appointment_Age[App_Not_Kept_To_TwentyNine].percent_not_kept) //TwentyNine not kept
    // var App_Not_Kept_To_ThirtyFour = Appointment_Age.findIndex(item => item.Age === '30-34');
    // var Final_Not_App_Kept_To_ThirtyFour = (Appointment_Age[App_Not_Kept_To_ThirtyFour].percent_not_kept) //ThirtyFour not kept
    // var App_Not_Kept_To_ThirtyNine = Appointment_Age.findIndex(item => item.Age === '35-39');
    // var Final_Not_App_Kept_To_ThirtyNine = (Appointment_Age[App_Not_Kept_To_ThirtyNine].percent_not_kept) //ThirtyNine not kept
    // var App_Not_Kept_To_FortyFour = Appointment_Age.findIndex(item => item.Age === '40-44');
    // var Final_Not_App_Kept_To_FortyFour = (Appointment_Age[App_Not_Kept_To_FortyFour].percent_not_kept) //FortyFour not kept
    // var App_Not_Kept_To_FortyNine = Appointment_Age.findIndex(item => item.Age === '45-49');
    // var Final_Not_App_Kept_To_FortyNine = (Appointment_Age[App_Not_Kept_To_FortyNine].percent_not_kept) //FortyNine not kept
    // var App_Not_Kept_To_FiftyFour = Appointment_Age.findIndex(item => item.Age === '50-54');
    // var Final_Not_App_Kept_To_FiftyFour = (Appointment_Age[App_Not_Kept_To_FiftyFour].percent_not_kept) //FiftyFour not kept
    // var App_Not_Kept_To_FiftyNine = Appointment_Age.findIndex(item => item.Age === '55-59');
    // var Final_Not_App_Kept_To_FiftyNine = (Appointment_Age[App_Not_Kept_To_FiftyNine].percent_not_kept) //FiftyNine not kept
    // var App_Not_Kept_To_SixtyFour = Appointment_Age.findIndex(item => item.Age === '60-64');
    // var Final_Not_App_Kept_To_SixtyFour = (Appointment_Age[App_Not_Kept_To_SixtyFour].percent_not_kept) //SixtyFour not kept
    // var App_Not_Kept_To_SixtyFivePlus = Appointment_Age.findIndex(item => item.Age === '65+');
    // var Final_Not_App_Kept_To_SixtyFivePlus = (Appointment_Age[App_Not_Kept_To_SixtyFivePlus].percent_not_kept) //SixtyFivePlus not kept

    // kept marital
    // var Appointment_Kept_Male = Appointment_Marital.findIndex(item => item.Gender === 'M');
    // var Final_Appointment_Kept_Male = (Appointment_Marital[Appointment_Kept_Male].Percent_Kept * 1) //male kept


    $('#verification_table_client').DataTable({
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
        "paging": true,
        "responsive": true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Clients List',
                filename: 'Clients List'
            }, 'excel', 'pdf', 'print'
        ]
    });

    var AppointmentAge = Highcharts.chart('appointment_age', {
        chart: {
            type: 'column',
        },
        title: {
            text: 'Appointment Distribution by Age'
        },
        style: {
            fontFamily: 'Manrope'
        },

        xAxis: {

            crosshair: true
        },
        yAxis: {
            max: 100,
            title: {
                useHTML: true,
                text: 'Percentage'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kept',
            data: [],
            color: '#01058A'

        }, {
            name: 'Not Kept',
            data: [],
            color: '#97080F'

        }]
    });

    var AppointmentGender = Highcharts.chart('appointment_gender', {
        chart: {
            type: 'column'
        },
        style: {
            fontFamily: 'Manrope'
        },
        title: {
            text: 'Appointment Distribution by Gender'
        },

        xAxis: {

            crosshair: true
        },
        yAxis: {
            max: 100,
            title: {
                useHTML: true,
                text: 'Percentage'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kept',
            color: '#01058A',
            data: []

        }, {
            name: 'Not Kept',
            color: '#97080F',
            data: []

        }]
    });

    var DistributionMarital = Highcharts.chart('distribution_marital', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Distribution by Marital Status'
        },
        xAxis: {


        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'Percentage'
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
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kept',
            color: '#01058A',
            data:[]

        }, {
            name: 'Not Kept',
            color: '#97080F',
            data:[]

        }],

    });

    var DistributionCounty = Highcharts.chart('distribution_county', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Distribution by County'
        },
        xAxis: {


        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'Percentage'
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
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kept',
            color: '#01058A',
            data:[]

        }, {
            name: 'Not Kept',
            color: '#97080F',
            data: []

        }],

    });

    var DistributionPartner = Highcharts.chart('distribution_partner', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Appointment Distribution by Partner'
        },
        xAxis: {

        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'Percentage'
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
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Kept',
            color: '#01058A',
            data: []

        }, {
            name: 'Not Kept',
            color: '#97080F',
            data: []

        }],

    });

    var MissedGender = Highcharts.chart('missed_gender', {
        chart: {
            type: 'column'
        },
        style: {
            fontFamily: 'Manrope'
        },
        title: {
            text: 'Missed Appointment Distribution by Gender'
        },

        xAxis: {
            categories: [
                'Female',
                'Male'
            ],
            crosshair: true
        },
        yAxis: {
            max: 100,
            title: {
                useHTML: true,
                text: 'Percentage'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Missed',
            color: '#01058A',
            data: []

        }, {
            name: 'Returned to care',
            color: '#97080F',
            data: []

        }]
    });

    var MissedAge = Highcharts.chart('missed_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Missed Appointment Distribution by Age'
        },

        xAxis: {
            categories: [
                '0-9',
                '10-14',
                '15-19',
                '20-24',
                '25-29',
                '30-34',
                '35-39',
                '40-44',
                '45-49',
                '50-54',
                '55-59',
                '60-64',
                '65+'
            ],
            crosshair: true
        },
        yAxis: {
            max: 100,
            title: {
                useHTML: true,
                text: 'Percentage'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Missed',
            data: [],
            color: '#01058A'

        }, {
            name: 'Returned to care',
            data: [],
            color: '#97080F'

        }]
    });

    var MissedMarital = Highcharts.chart('missed_marital', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Missed Client Distribution by Marital Status'
        },
        xAxis: {
            categories: []
        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'Percentage'
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
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Missed',
            color: '#01058A',
            data: []

        }, {
            name: 'Returned to care',
            color: '#97080F',
            data: []

        }],

    });
</script>





<!-- end of col -->

@endsection