@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

<style rel="stylesheet" type="text/css">
.tab-content {
  font-family: Manrope;
  font-size: 18px;
  color:#FFFFFF;
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
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">Client Verification Dashboard</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">
        <div class="col-md-12" >
            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75" style="background-color:#369FFF; border-radius: 1em / 1em;">
                        <div class="card-body" >
                            <div class="content">
                                <p class="line-height-1 mb-2">{{ number_format(json_decode($verification_count_total[0]->total)) }}</p>
                                <p>Registered</p>
                                <p style="padding:5px"></p>
                            </div>
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75" style="background-color:#8AC53E; border-radius: 1em / 1em;">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <p class="line-height-1 mb-2">{{ number_format(json_decode($verification_count_total[0]->verified)) }}</p>
                                <p>Verified</p>
                                <div style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px; margin-bottom: 10px;"><h2 style="font-size: 10px; margin-top: 15px;">{{ json_decode($verification_count_total[0]->percenct_verified) }}%</h2></div>


                            </div>
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75" style="background-color:#663399; border-radius: 1em / 1em;">
                        <div class="card-body">
                            <div class="content" id="maindiv">
                                <p class="line-height-1 mb-2">{{ number_format(json_decode($verification_count_total[0]->not_verified)) }}</p>
                                <p style ="white-space: nowrap ;">Not Verified</p>

                                <div style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px; margin-bottom: 10px;"><h2 style="font-size: 10px; margin-top: 15px;">{{ json_decode($verification_count_total[0]->percenct_not_verified) }}%</h2></div>

                            </div>
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-6">

                    <div class="card-body row">
                        <div id="Verified_Not_Verified" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
                <div class="col-6">

                    <div class="card-body row">
                        <div id="verification_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-6">
                    <div class="card-body row">
                        <div id="verification_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>

                <div class="col-6">
                    <div class="card-body row">
                        <div id="verification_facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
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
                            <h4 class="card-title mb-3">Client Verification Status</h4>
                            <div class="table-responsive">
                                <table id="verification_table_client" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Clinic Number</th>
                                            <th>Client Name</th>
                                            <th>Verification Status</th>
                                            <th>Phone Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($verification_list) > 0)
                                        @foreach($verification_list as $result)
                                        <tr>
                                            <td> {{ $loop->iteration }}</td>
                                            <td> {{$result->clinic_number}}</td>
                                            <td> {{$result->client_name}}</td>
                                            <td> {{$result->verification_status}}</td>
                                            <td>{{$result->mobile_no}}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        @endif
                        @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
                        <div class="card-body">
                            <h4 class="card-title mb-3">Verification Status by Facility</h4>
                            <div class="table-responsive">
                                <table id="verification_table_partner" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>MFL Code</th>
                                            <th>Facility Name</th>
                                            <th>Registered Clients</th>
                                            <th>Verified</th>
                                            <th>Not Verified</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($verification_list) > 0)
                                        @foreach($verification_list as $result)
                                        <tr>
                                            <td> {{ $loop->iteration }}</td>
                                            <td> {{$result->mfl_code}}</td>
                                            <td> {{$result->name}}</td>
                                            <td> {{$result->total}}</td>
                                            <td> {{$result->verified}}</td>
                                            <td> {{$result->not_verified}}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        @endif
                        @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
                        <div class="card-body">
                            <h4 class="card-title mb-3">Verification Status by Partner</h4>
                            <div class="table-responsive">
                                <table id="verification_table_national" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Partner Name</th>
                                            <th>Registered Clients</th>
                                            <th>Verified</th>
                                            <th>Not Verified</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($verification_list) > 0)
                                        @foreach($verification_list as $result)
                                        <tr>
                                            <td> {{ $loop->iteration }}</td>
                                            <td> {{$result->name}}</td>
                                            <td> {{$result->total}}</td>
                                            <td> {{$result->verified}}</td>
                                            <td> {{$result->not_verified}}</td>
                                        </tr>
                                        @endforeach
                                        @endif
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

    $('#verification_table_partner').DataTable({
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
                title: 'Facility List',
                filename: 'Facility List'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Facility List',
                filename: 'Facility List'
            }, 'excel', 'pdf', 'print'
        ]
    });
    $('#verification_table_national').DataTable({
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
                title: 'Partner List',
                filename: 'Partner List'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Partner List',
                filename: 'Partner List'
            }, 'excel', 'pdf', 'print'
        ]
    });
    // appointment
    var Clients_Verification_Age = <?php echo json_encode($verification_age) ?>;
    var Clients_Verification_Gender = <?php echo json_encode($verification_gender) ?>;
    var Verification_Facility = <?php echo json_encode($verification_count) ?>;
    var Verified_Vs_Not_Verified = <?php echo json_encode($verification_count_total) ?>;

    console.log(Verified_Vs_Not_Verified);

    // verified age
    var Verified_To_Nine = Clients_Verification_Age.findIndex(item => item.Age === '0-9');
    var Final_Verified_To_Nine = (Clients_Verification_Age[Verified_To_Nine].percenct_verified * 1) //nine verified
    var Verified_To_Fourteen = Clients_Verification_Age.findIndex(item => item.Age === '10-14');
    var Final_Verified_To_Fourteen = (Clients_Verification_Age[Verified_To_Fourteen].percenct_verified * 1) //fourteen verified
    var Verified_To_Nineteen = Clients_Verification_Age.findIndex(item => item.Age === '15-19');
    var Final_Verified_To_Nineteen = (Clients_Verification_Age[Verified_To_Nineteen].percenct_verified * 1) //Nineteen verified
    var Verified_To_Tweentyfour = Clients_Verification_Age.findIndex(item => item.Age === '20-24');
    var Final_Verified_To_Tweentyfour = (Clients_Verification_Age[Verified_To_Tweentyfour].percenct_verified * 1)
    var Verified_To_Tweentynine = Clients_Verification_Age.findIndex(item => item.Age === '25-29');
    var Final_Verified_To_Tweentynine = (Clients_Verification_Age[Verified_To_Tweentynine].percenct_verified * 1) //Tweentynine verified
    var Verified_To_Thirtyfour = Clients_Verification_Age.findIndex(item => item.Age === '30-34');
    var Final_Verified_To_Thirtyfour = (Clients_Verification_Age[Verified_To_Thirtyfour].percenct_verified * 1) //Thirtyfour verified
    var Verified_To_Thirtynine = Clients_Verification_Age.findIndex(item => item.Age === '35-39');
    var Final_Verified_To_Thirtynine = (Clients_Verification_Age[Verified_To_Thirtynine].percenct_verified * 1) //Thirtynine verified
    var Verified_To_Fortyfour = Clients_Verification_Age.findIndex(item => item.Age === '40-44');
    var Final_Verified_To_Fortyfour = (Clients_Verification_Age[Verified_To_Fortyfour].percenct_verified * 1) //Fortyfour verified
    var Verified_To_Fortynine = Clients_Verification_Age.findIndex(item => item.Age === '45-49');
    var Final_Verified_To_Fortynine = (Clients_Verification_Age[Verified_To_Fortynine].percenct_verified * 1) //Fortynine verified
    var Verified_To_Fiftyfour = Clients_Verification_Age.findIndex(item => item.Age === '50-54');
    var Final_Verified_To_Fiftyfour = (Clients_Verification_Age[Verified_To_Fiftyfour].percenct_verified * 1) //Fiftyfour verified
    var Verified_To_Fiftynine = Clients_Verification_Age.findIndex(item => item.Age === '55-59');
    var Final_Verified_To_Fiftynine = (Clients_Verification_Age[Verified_To_Fiftynine].percenct_verified * 1) //Fiftynine verified
    var Verified_To_Sixtyfour = Clients_Verification_Age.findIndex(item => item.Age === '60-64');
    var Final_Verified_To_Sixtyfour = (Clients_Verification_Age[Verified_To_Sixtyfour].percenct_verified * 1) //Sixtyfour verified
    var Verified_To_Sixtyfive = Clients_Verification_Age.findIndex(item => item.Age === '65+');
    var Final_Verified_To_Sixtyfive = (Clients_Verification_Age[Verified_To_Sixtyfive].percenct_verified * 1) //Sixtyfive verified

    console.log(Clients_Verification_Age);
    // not verified age
    var Not_Verified_To_Nine = Clients_Verification_Age.findIndex(item => item.Age === '0-9');
    var Final_Not_Verified_To_Nine = (Clients_Verification_Age[Not_Verified_To_Nine].percenct_not_verified * 1) //nine verified
    var Not_Verified_To_Fourteen = Clients_Verification_Age.findIndex(item => item.Age === '10-14');
    var Final_Not_Verified_To_Fourteen = (Clients_Verification_Age[Not_Verified_To_Fourteen].percenct_not_verified * 1) //fourteen verified
    var Not_Verified_To_Nineteen = Clients_Verification_Age.findIndex(item => item.Age === '15-19');
    var Final_Not_Verified_To_Nineteen = (Clients_Verification_Age[Not_Verified_To_Nineteen].percenct_not_verified * 1) //Nineteen verified
    var Not_Verified_To_Tweentyfour = Clients_Verification_Age.findIndex(item => item.Age === '20-24');
    var Final_Not_Verified_To_Tweentyfour = (Clients_Verification_Age[Not_Verified_To_Tweentyfour].percenct_not_verified * 1)
    var Not_Verified_To_Tweentynine = Clients_Verification_Age.findIndex(item => item.Age === '25-29');
    var Final_Not_Verified_To_Tweentynine = (Clients_Verification_Age[Not_Verified_To_Tweentynine].percenct_not_verified * 1) //Tweentynine verified
    var Not_Verified_To_Thirtyfour = Clients_Verification_Age.findIndex(item => item.Age === '30-34');
    var Final_Not_Verified_To_Thirtyfour = (Clients_Verification_Age[Not_Verified_To_Thirtyfour].percenct_not_verified * 1) //Thirtyfour verified
    var Not_Verified_To_Thirtynine = Clients_Verification_Age.findIndex(item => item.Age === '35-39');
    var Final_Not_Verified_To_Thirtynine = (Clients_Verification_Age[Not_Verified_To_Thirtynine].percenct_not_verified * 1) //Thirtynine verified
    var Not_Verified_To_Fortyfour = Clients_Verification_Age.findIndex(item => item.Age === '40-44');
    var Final_Not_Verified_To_Fortyfour = (Clients_Verification_Age[Not_Verified_To_Fortyfour].percenct_not_verified * 1) //Fortyfour verified
    var Not_Verified_To_Fortynine = Clients_Verification_Age.findIndex(item => item.Age === '45-49');
    var Final_Not_Verified_To_Fortynine = (Clients_Verification_Age[Not_Verified_To_Fortynine].percenct_not_verified * 1) //Fortynine verified
    var Not_Verified_To_Fiftyfour = Clients_Verification_Age.findIndex(item => item.Age === '50-54');
    var Final_Not_Verified_To_Fiftyfour = (Clients_Verification_Age[Not_Verified_To_Fiftyfour].percenct_not_verified * 1) //Fiftyfour verified
    var Not_Verified_To_Fiftynine = Clients_Verification_Age.findIndex(item => item.Age === '55-59');
    var Final_Not_Verified_To_Fiftynine = (Clients_Verification_Age[Not_Verified_To_Fiftynine].percenct_not_verified * 1) //Fiftynine verified
    var Not_Verified_To_Sixtyfour = Clients_Verification_Age.findIndex(item => item.Age === '60-64');
    var Final_Not_Verified_To_Sixtyfour = (Clients_Verification_Age[Not_Verified_To_Sixtyfour].percenct_not_verified * 1) //Sixtyfour verified
    var Not_Verified_To_Sixtyfive = Clients_Verification_Age.findIndex(item => item.Age === '65+');
    var Final_Not_Verified_To_Sixtyfive = (Clients_Verification_Age[Not_Verified_To_Sixtyfive].percenct_not_verified * 1) //Sixtyfive verified

    console.log(Final_Not_Verified_To_Fourteen);

    // verified vs not verified gender
    var Verified_Male = Clients_Verification_Gender.findIndex(item => item.Gender === 'M');
    var Final_Verified_Male = (Clients_Verification_Gender[Verified_Male].percenct_verified * 1) //male verified
    var Verified_Female = Clients_Verification_Gender.findIndex(item => item.Gender === 'F');
    var Final_Verified_Female = (Clients_Verification_Gender[Verified_Female].percenct_verified * 1) //female verified
    var Not_Verified_Male = Clients_Verification_Gender.findIndex(item => item.Gender === 'M');
    var Final_Not_Verified_Male = (Clients_Verification_Gender[Not_Verified_Male].percenct_not_verified * 1) //male not verified
    var Not_Verified_Female = Clients_Verification_Gender.findIndex(item => item.Gender === 'F');
    var Final_Not_Verified_Female = (Clients_Verification_Gender[Not_Verified_Female].percenct_not_verified * 1) //female not verified

    // verified vs not verified summary
    var Verified = (Verified_Vs_Not_Verified[0].percenct_verified * 1) //verified
    var Not_Verified = (Verified_Vs_Not_Verified[0].percenct_not_verified * 1) //not verified


    var VerificationAge = Highcharts.chart('verification_age', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Verification Status by Age'
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
            name: 'Verified',
            data: [Final_Verified_To_Nine, Final_Verified_To_Fourteen, Final_Verified_To_Nineteen, Final_Verified_To_Tweentyfour, Final_Verified_To_Tweentynine, Final_Verified_To_Thirtyfour, Final_Verified_To_Thirtynine,
                Final_Verified_To_Fortyfour, Final_Verified_To_Fortynine, Final_Verified_To_Fiftyfour, Final_Verified_To_Fiftynine, Final_Verified_To_Sixtyfour, Final_Verified_To_Sixtyfive
            ],
            color: '#01058A'

        }, {
            name: 'Not Verified',
            data: [Final_Not_Verified_To_Nine, Final_Not_Verified_To_Fourteen, Final_Not_Verified_To_Nineteen, Final_Not_Verified_To_Tweentyfour, Final_Not_Verified_To_Tweentynine, Final_Not_Verified_To_Thirtyfour, Final_Not_Verified_To_Thirtynine,
                Final_Not_Verified_To_Fortyfour, Final_Not_Verified_To_Fortynine, Final_Not_Verified_To_Fiftyfour, Final_Not_Verified_To_Fiftynine, Final_Not_Verified_To_Sixtyfour, Final_Not_Verified_To_Sixtyfive
            ],
            color: '#97080F'

        }]
    });


    var VerificationFacility = Highcharts.chart('verification_facility', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Verification Status by Facility'
        },
        xAxis: {
            categories: Verification_Facility.map(function(x) {
                return x.name;
            })
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
            name: 'Verified',
            color: '#01058A',
            data: Verification_Facility.map(function(x) {
                return {
                    name: x.name,
                    y: parseInt(x.percenct_verified, 10)
                }
            })

        }, {
            name: 'Not Verified',
            color: '#97080F',
            data: Verification_Facility.map(function(x) {
                return {
                    name: x.name,
                    y: parseInt(x.percenct_not_verified, 10)
                }
            })

        }],

    });

    var VerificationGender = Highcharts.chart('verification_gender', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Verification Status by Gender'
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
            name: 'Verified',
            color: '#01058A',
            data: [Final_Verified_Female, Final_Verified_Male]

        }, {
            name: 'Not Verified',
            color: '#97080F',
            data: [Final_Not_Verified_Female, Final_Not_Verified_Male]

        }]
    });
    $(function() {
        // var name = ['Verified', 'firefox', 'opera'];
        // var data = [Final_Verified_To_Nine, Final_Not_Verified_To_Nine];
        // var final = [];

        // for (var i = 0; i < name.length; i++) {
        //     final.push({
        //         name: name[i],
        //         y: data[i]
        //     });
        // }
        var VerifiedNotVerified = Highcharts.chart('Verified_Not_Verified', {
            chart: {
                type: 'variablepie'
            },
            title: {
                text: 'Verified Vs Not Verified'
            },
            tooltip: {
                headerFormat: '',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                    'Pecentage: <b>{point.y}</b><br/>'
            },
            series: [{
                minPointSize: 10,
                innerSize: '20%',
                zMin: 0,
                name: 'Verification',
                data: [{
                    name: 'Verified',
                    color: '#01058A',
                    y: Verified,
                    z: 92
                }, {
                    name: 'Not Verified',
                    y: Not_Verified,
                    color: '#97080F',
                    z: 235
                }]
            }]
        });
    });


</script>





<!-- end of col -->

@endsection