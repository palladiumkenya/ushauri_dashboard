@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
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

    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-sm-2 col-md-2 col-lg-2">
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
            <div class="col-sm-2 col-md-2 col-lg-2">
                <div class="form-group">

                    <select class="form-control county  input-rounded input-sm select2" id="counties" name="county">
                        <option value="">County:</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                <div class="form-group">

                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value=""> Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">.</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class='col-sm-2 col-md-2 col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <!-- <div class="col-md-2">
                            <label for="firstName1">From</label>
                        </div> -->

                        <!-- <div class="col-md-6"> -->

                        <input type="date" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}">
                        <!-- </div> -->
                        <!-- <div class="input-group-append">

                        </div> -->
                    </div>
                </div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <!-- <div class="col-md-2">
                            <label for="firstName1">To</label>
                        </div> -->
                        <!-- <div class="col-md-6"> -->

                        <input type="date" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}">
                        <!-- </div> -->
                        <!-- <div class="input-group-append">

                        </div> -->
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

            <div class='col-lg-4'>
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
            <div class="col-lg-4">
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
            <div class="col-lg-4">
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
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">UPTAKE</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-appointment" role="tab" aria-selected="false">APPOINTMENT MANAGEMENT</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-statistics" role="tab" aria-selected="false">STATISTICS</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">


        <div id="highchart"></div>
        <div class="row">


            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body">
                        <div class="content">
                            <p class="text-center text-muted mt-2 mb-0">TX Curr </p>

                            <p id="txcurr" class="text-center text-primary text-20 line-height-1 mb-1">{{ number_format(json_decode($txcur[0]->tx_cur)) }}</p>
                            <p class="text-center text-primary text-12 line-height-1 mb-1">Source KHIS </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Registered</p>

                            <p id="registered_ushauri" class="text-primary text-20 line-height-1 mb-1">{{ number_format(json_decode($registered[0]->registeredClients)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Consented</p>

                            <p id="consented_sms" class="text-primary text-20 line-height-1 mb-2">{{ number_format(json_decode($consented[0]->consented)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">SMS Sent</p>
                            <a class="has-arrow">
                                <p id="client" class="text-primary text-20 line-height-1 mb-2">{{ number_format(json_decode($messages_sent[0]->sentmesseges)) }}</p>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="uptake_cascade" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="consented_Age_Sex" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="sms_Age_Sex" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="consented_Facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>

    </div>

    <!-- appointment tab -->
    <div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">


        <div id="highchart"></div>
        <div class="row">


            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Active </p>

                            <p id="facilities_ever_enrolled" class="text-primary text-20 line-height-1 mb-1"> {{ number_format(json_decode($registered[0]->registeredClients)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Scheduled Appointments </p>

                            <p id="scheduled_appointments" class="text-primary text-20 line-height-1 mb-1">{{ number_format(json_decode($scheduledappointment[0]->appointments)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0">Received SMS</p>

                            <p id="received_sms" class="text-primary text-20 line-height-1 mb-2">{{ number_format(json_decode($messages_sent[0]->sentmesseges)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 ">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4 h-75">
                    <div class="card-body text-center">
                        <div class="content">
                            <p class="text-muted mt-2 mb-0"> Honoured Appointments</p>
                            <a class="has-arrow" href="{{route('client_dashboard')}}">
                                <p id="honored_appointments" class="text-primary text-20 line-height-1 mb-2">{{number_format(json_decode($honoredappointment[0]->honored))}}</p>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="appointment_uptake" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="received_sms_Age_Sex" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

            <div class="col-6">

                <div class="card-body row">
                    <div id="honoured_Age_Sex" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="honoured_Facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
        </div>

    </div>

    <!-- statistic tab -->
    <div class="tab-pane fade" id="nav-statistics" role="tabpanel" aria-labelledby="nav-statistics-tab">


        <div id="highchart"></div>
        <div class="row">


            <div class="col-lg-3">

            </div>

            <div class="col-md-12 mb-4">
                <div class="card text-left">

                    <div class="card-body">
                        <h4 class="card-title mb-3"></h4>
                        <div class="table-responsive">
                            <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Partner</th>
                                        <th>Facility</th>
                                        <th>Mfl Code</th>
                                        <th>Registered Clients</th>
                                        <th>Consented Clients</th>
                                        <th>Appointments</th>
                                        <th>Honored Appointments</th>
                                        <th>Messages Sent</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($ushauristatistics) > 0)
                                    @foreach($ushauristatistics as $data)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{$data->partner_name}}</td>
                                        <td> {{$data->facility_name}}</td>
                                        <td> {{$data->mfl_code}}</td>
                                        <td> {{$data->registered_clients}}</td>
                                        <td> {{$data->consented}}</td>
                                        <td> {{$data->appointments}}</td>
                                        <td> {{$data->honored}}</td>
                                        <td> {{$data->messeges}}</td>


                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>

                            </table>

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
        // multi column ordering
        $('#multicolumn_ordering_table').DataTable({
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
                    title: 'USHAURI STATISTIC',
                    filename: 'Ushauri data'
                },
                {
                    extend: 'csv',
                    title: 'USHAURI STATISTIC',
                    filename: 'Ushauri data'
                },
                {
                    extend: 'excel',
                    title: 'USHAURI STATISTIC',
                    filename: 'Ushauri data'
                },
                {
                    extend: 'pdf',
                    title: 'USHAURI STATISTIC',
                    filename: 'Ushauri data'
                },
                {
                    extend: 'print',
                    title: 'USHAURI STATISTIC',
                    filename: 'Ushauri data'
                }
            ]
        });

        var Ushauri_Statistics = <?php echo json_encode($ushauristatistics) ?>;

        var Facility_Name = <?php echo json_encode($ushauristatistics[0]->facility_name) ?>;
        var Mfl_Code = <?php echo json_encode($ushauristatistics[0]->mfl_code) ?>;
        var Consented = <?php echo json_encode($ushauristatistics[0]->consented) ?>;
        var Appointment = <?php echo json_encode($ushauristatistics[0]->appointments) ?>;


        var Clients_Registered = <?php echo json_decode($registered[0]->registeredClients) ?>;
        var Clients_Consented = <?php echo json_decode($consented[0]->consented) ?>;
        var Txcurr = <?php echo json_decode($txcur[0]->tx_cur) ?>;
        var Sms_Sent = <?php echo json_decode($messages_sent[0]->sentmesseges) ?>;
        var Facilitie_Honored = <?php echo json_encode($honoredappointmentfacilities) ?>;
        var Facilitie_Consented = <?php echo json_encode($consentedfacilities) ?>;
        console.log(Txcurr);

        var Clients_Consent_Age_Sex = <?php echo json_encode($consentedagesex) ?>;
        var Consented_Age_Sex_Male_Nineteen = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '15-19');
        var Final_Consented_Age_Sex_Male_Nineteen = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_Nineteen].No //male 15-19
        var Consented_Age_Sex_Female_Nineteen = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '15-19');
        var Final_Consented_Age_Sex_Female_Nineteen = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_Nineteen].No //female 15-19
        var Consented_Age_Sex_Male_TwentyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '20-24');
        var Final_Consented_Age_Sex_Male_TwentyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_TwentyFour].No //male 20-24
        var Consented_Age_Sex_Female_TwentyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '20-24');
        var Final_Consented_Age_Sex_Female_TwentyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_TwentyFour].No //female 20-24
        var Consented_Age_Sex_Male_TwentyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '25-29');
        var Final_Consented_Age_Sex_Male_TwentyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_TwentyNine].No //male 25-29
        var Consented_Age_Sex_Female_TwentyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '25-29');
        var Final_Consented_Age_Sex_Female_TwentyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_TwentyNine].No //female 25-29
        var Consented_Age_Sex_Male_ThirtyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '30-34');
        var Final_Consented_Age_Sex_Male_ThirtyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_ThirtyFour].No //male 30-34
        var Consented_Age_Sex_Female_ThirtyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '30-34');
        var Final_Consented_Age_Sex_Female_ThirtyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_ThirtyFour].No //female 30-34
        var Consented_Age_Sex_Male_ThirtyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '35-39');
        var Final_Consented_Age_Sex_Male_ThirtyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_ThirtyNine].No //male 35-39
        var Consented_Age_Sex_Female_ThirtyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '35-39');
        var Final_Consented_Age_Sex_Female_ThirtyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_ThirtyNine].No //female 35-39
        var Consented_Age_Sex_Male_FourtyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '40-44');
        var Final_Consented_Age_Sex_Male_FourtyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_FourtyFour].No //male 40-44
        var Consented_Age_Sex_Female_FourtyFour = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '40-44');
        var Final_Consented_Age_Sex_Female_FourtyFour = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_FourtyFour].No //female 40-44
        var Consented_Age_Sex_Male_FourtyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '45-49');
        var Final_Consented_Age_Sex_Male_FourtyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_FourtyNine].No //male 45-49
        var Consented_Age_Sex_Female_FourtyNine = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '45-49');
        var Final_Consented_Age_Sex_Female_FourtyNine = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_FourtyNine].No //female 45-49
        var Consented_Age_Sex_Male_Fifty = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '50+');
        var Final_Consented_Age_Sex_Male_Fifty = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_Fifty].No //male 50+
        var Consented_Age_Sex_Female_Fifty = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '50+');
        var Final_Consented_Age_Sex_Female_Fifty = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_Fifty].No //female 50+
        var Consented_Age_Sex_Male_Uknown = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === 'Unknown Age');
        var Final_Consented_Age_Sex_Male_Uknown = Clients_Consent_Age_Sex[Consented_Age_Sex_Male_Uknown].No //male uknown
        // var Consented_Age_Sex_Female_Uknown = Clients_Consent_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === 'Unknown Age');
        // var Final_Consented_Age_Sex_Female_Uknown = Clients_Consent_Age_Sex[Consented_Age_Sex_Female_Uknown].No //female uknown
        //  console.log(Final_Consented_Age_Sex_Male_Nineteen);


        var Scheduled_Appointment = <?php echo json_decode($scheduledappointment[0]->appointments) ?>;
        var Honored_Appointment = <?php echo json_decode($honoredappointment[0]->honored) ?>;

        var App_Honored_Age_Sex = <?php echo json_encode($honoredappointmentagesex) ?>;
        var Honored_Age_Sex_Male_Nineteen = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '15-19');
        var Final_Honored_Age_Sex_Male_Nineteen = App_Honored_Age_Sex[Honored_Age_Sex_Male_Nineteen].No //male 15-19
        // var Honored_Age_Sex_Female_Nineteen = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '15-19');
        // var Final_Honored_Age_Sex_Female_Nineteen = App_Honored_Age_Sex[Honored_Age_Sex_Female_Nineteen].No //female 15-19
        var Honored_Age_Sex_Male_TwentyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '20-24');
        var Final_Honored_Age_Sex_Male_TwentyFour = App_Honored_Age_Sex[Honored_Age_Sex_Male_TwentyFour].No //male 20-24
        var Honored_Age_Sex_Female_TwentyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '20-24');
        var Final_Honored_Age_Sex_Female_TwentyFour = App_Honored_Age_Sex[Honored_Age_Sex_Female_TwentyFour].No //female 20-24
        var Honored_Age_Sex_Male_TwentyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '25-29');
        var Final_Honored_Age_Sex_Male_TwentyNine = App_Honored_Age_Sex[Honored_Age_Sex_Male_TwentyNine].No //male 25-29
        var Honored_Age_Sex_Female_TwentyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '25-29');
        var Final_Honored_Age_Sex_Female_TwentyNine = App_Honored_Age_Sex[Honored_Age_Sex_Female_TwentyNine].No //female 25-29
        var Honored_Age_Sex_Male_ThirtyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '30-34');
        var Final_Honored_Age_Sex_Male_ThirtyFour = App_Honored_Age_Sex[Honored_Age_Sex_Male_ThirtyFour].No //male 30-34
        var Honored_Age_Sex_Female_ThirtyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '30-34');
        var Final_Honored_Age_Sex_Female_ThirtyFour = App_Honored_Age_Sex[Honored_Age_Sex_Female_ThirtyFour].No //female 30-34
        var Honored_Age_Sex_Male_ThirtyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '35-39');
        var Final_Honored_Age_Sex_Male_ThirtyNine = App_Honored_Age_Sex[Honored_Age_Sex_Male_ThirtyNine].No //male 35-39
        var Honored_Age_Sex_Female_ThirtyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '35-39');
        var Final_Honored_Age_Sex_Female_ThirtyNine = App_Honored_Age_Sex[Honored_Age_Sex_Female_ThirtyNine].No //female 35-39
        var Honored_Age_Sex_Male_FourtyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '40-44');
        var Final_Honored_Age_Sex_Male_FourtyFour = App_Honored_Age_Sex[Honored_Age_Sex_Male_FourtyFour].No //male 40-44
        var Honored_Age_Sex_Female_FourtyFour = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '40-44');
        var Final_Honored_Age_Sex_Female_FourtyFour = App_Honored_Age_Sex[Honored_Age_Sex_Female_FourtyFour].No //female 40-44
        var Honored_Age_Sex_Male_FourtyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '45-49');
        var Final_Honored_Age_Sex_Male_FourtyNine = App_Honored_Age_Sex[Honored_Age_Sex_Male_FourtyNine].No //male 45-49
        var Honored_Age_Sex_Female_FourtyNine = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '45-49');
        var Final_Honored_Age_Sex_Female_FourtyNine = App_Honored_Age_Sex[Honored_Age_Sex_Female_FourtyNine].No //female 45-49
        var Honored_Age_Sex_Male_Fifty = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === '50+');
        var Final_Honored_Age_Sex_Male_Fifty = App_Honored_Age_Sex[Honored_Age_Sex_Male_Fifty].No //male 50+
        var Honored_Age_Sex_Female_Fifty = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === '50+');
        var Final_Honored_Age_Sex_Female_Fifty = App_Honored_Age_Sex[Honored_Age_Sex_Female_Fifty].No //female 50+
        // var Honored_Age_Sex_Male_Uknown = App_Honored_Age_Sex.findIndex(item => item.Gender === "M" && item.Age === 'Unknown Age');
        // var Final_Honored_Age_Sex_Male_Uknown = App_Honored_Age_Sex[Honored_Age_Sex_Male_Uknown].No //male uknown
        // var Honored_Age_Sex_Female_Uknown = App_Honored_Age_Sex.findIndex(item => item.Gender === "F" && item.Age === 'Unknown Age');
        // var Final_Honored_Age_Sex_Female_Uknown = App_Honored_Age_Sex[Honored_Age_Sex_Female_Uknown].No //female uknown



        var consentedAgeSex = Highcharts.chart('consented_Age_Sex', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Consented By Age and Sex'
            },

            xAxis: {
                categories: [
                    '15-19',
                    '20-24',
                    '25-29',
                    '30-34',
                    '35-39',
                    '40-44',
                    '45-49',
                    '50+',
                    'Uknown Age'
                ],
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Percent of Patient'
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
                name: 'Male',
                data: [Final_Consented_Age_Sex_Male_Nineteen, Final_Consented_Age_Sex_Male_TwentyFour, Final_Consented_Age_Sex_Male_TwentyNine, Final_Consented_Age_Sex_Male_ThirtyFour, Final_Consented_Age_Sex_Male_ThirtyNine, Final_Consented_Age_Sex_Male_FourtyFour, Final_Consented_Age_Sex_Male_FourtyNine,
                    Final_Consented_Age_Sex_Male_Fifty, Final_Consented_Age_Sex_Male_Uknown
                ]

            }, {
                name: 'Female',
                data: [Final_Consented_Age_Sex_Female_Nineteen, Final_Consented_Age_Sex_Female_TwentyFour, Final_Consented_Age_Sex_Female_TwentyNine, Final_Consented_Age_Sex_Female_ThirtyFour, Final_Consented_Age_Sex_Female_ThirtyNine, Final_Consented_Age_Sex_Female_FourtyFour, Final_Consented_Age_Sex_Female_FourtyNine,
                    Final_Consented_Age_Sex_Female_Fifty, 0
                ]

            }]
        });

        var uptakeCascade = Highcharts.chart('uptake_cascade', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Uptake Cascade'
            },
            xAxis: {
                categories: ['TX CURR', 'REGISTERED IN USHAURI', 'CONSENTED FOR SMS', 'ACTIVELY RECEIVING SMS']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Percent of Patient'
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
                name: 'Uptake',
                data: [Txcurr, Clients_Registered, Clients_Consented, 0]
            }],

        });
        var smsAgeSex = Highcharts.chart('sms_Age_Sex', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Actively Receiving SMS By Age and Sex'
            },

            xAxis: {
                categories: [
                    '15-19',
                    '20-24',
                    '25-29',
                    '30-34',
                    '35-39',
                    '40-44',
                    '45-49',
                    '50+',
                    'Uknown Age'
                ],
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Percent of Patient'
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
                name: 'Male',
                data: [13000, 23000, 56000, 29000, 15000, 12000, 8000,
                    11000, 60
                ]

            }, {
                name: 'Female',
                data: [19000, 27000, 78000, 34000, 25000, 18000, 9500,
                    8000, 40
                ]

            }]
        });

        var consentedFacility = Highcharts.chart('consented_Facility', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Consented By Facility'
            },
            xAxis: {
                categories: Facilitie_Consented.map(function(x) {
                    return x.fac_name;
                })
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Percent of Patient'
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
                name: 'Facility',
                data: Facilitie_Consented.map(function(x) {
                    return {
                        name: x.fac_name,
                        y: parseInt(x.No, 10)
                    }
                })
            }],

        });

        // appointment

        var ReceivedSmsAgeSex = Highcharts.chart('received_sms_Age_Sex', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Received SMS By Age and Sex'
            },

            xAxis: {
                categories: [
                    '15-19',
                    '20-24',
                    '25-29',
                    '30-34',
                    '35-39',
                    '40-44',
                    '45-49',
                    '50+',
                    'Uknown Age'
                ],
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Percent of Patient'
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
                name: 'Male',
                data: [13000, 23000, 56000, 29000, 15000, 12000, 8000,
                    11000, 60
                ]

            }, {
                name: 'Female',
                data: [19000, 27000, 78000, 34000, 25000, 18000, 9500,
                    8000, 40
                ]

            }]
        });

        var appointmentuptakeCascade = Highcharts.chart('appointment_uptake', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Uptake Cascade'
            },
            xAxis: {
                categories: ['ACTIVE', 'SCHEDULED APPOINTMENTS', 'RECEIVED SMS', 'HONOURED APPOINTMENTS']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Percent of Patient'
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
                name: 'Uptake',
                data: [Clients_Registered, Scheduled_Appointment, Sms_Sent, Honored_Appointment]
            }],

        });
        var honouredAgeSex = Highcharts.chart('honoured_Age_Sex', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Honoured Appointments By Age and Sex'
            },

            xAxis: {
                categories: [
                    '15-19',
                    '20-24',
                    '25-29',
                    '30-34',
                    '35-39',
                    '40-44',
                    '45-49',
                    '50+',
                    'Uknown Age'
                ],
                crosshair: true
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: 'Percent of Patient'
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
                name: 'Male',
                data: [Final_Honored_Age_Sex_Male_Nineteen, Final_Honored_Age_Sex_Male_TwentyFour, Final_Honored_Age_Sex_Male_TwentyNine, Final_Honored_Age_Sex_Male_ThirtyFour, Final_Honored_Age_Sex_Male_ThirtyNine, Final_Honored_Age_Sex_Male_FourtyFour, Final_Honored_Age_Sex_Male_FourtyNine,
                    Final_Honored_Age_Sex_Male_Fifty, 0
                ]

            }, {
                name: 'Female',
                data: [0, Final_Honored_Age_Sex_Female_TwentyFour, Final_Honored_Age_Sex_Female_TwentyNine, Final_Honored_Age_Sex_Female_ThirtyFour, Final_Honored_Age_Sex_Female_ThirtyNine, Final_Honored_Age_Sex_Female_FourtyFour, Final_Honored_Age_Sex_Female_FourtyNine,
                    Final_Honored_Age_Sex_Female_Fifty, 0
                ]

            }]
        });

        var honouredFacility = Highcharts.chart('honoured_Facility', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Honoured Appointments By Facility'
            },
            xAxis: {
                categories: Facilitie_Honored.map(function(x) {
                    return x.fac_name;
                })
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Percent of Patient'
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
                name: 'Facility',
                data: Facilitie_Honored.map(function(x) {
                    return {
                        name: x.fac_name,
                        y: parseInt(x.No, 10)
                    }
                })
            }],

        });
        // Highcharts.drawTable = function() {


        //     // user options
        //     var tableTop = 55,
        //         colWidth = 110,
        //         tableLeft = 40,
        //         rowHeight = 40,
        //         cellPadding = 6,
        //         valueDecimals = 0;

        //     var chart = this,
        //         series = chart.series,
        //         renderer = chart.renderer,
        //         cellLeft = tableLeft;

        //     // draw category labels
        //     $.each(chart.xAxis[0].categories, function(i, name) {
        //         renderer.text(
        //                 name,
        //                 cellLeft + cellPadding,
        //                 tableTop + (i + 2) * rowHeight - cellPadding
        //             )
        //             .css({
        //                 fontWeight: 'bold'
        //             })
        //             .add();
        //     });

        //     $.each(series, function(i, serie) {
        //         cellLeft += colWidth;

        //         // Apply the cell text
        //         renderer.text(
        //                 serie.name,
        //                 cellLeft - cellPadding + colWidth,
        //                 tableTop + rowHeight - cellPadding
        //             )
        //             .attr({
        //                 align: 'right'
        //             })
        //             .css({
        //                 fontWeight: 'bold'
        //             })
        //             .add();

        //         $.each(serie.data, function(row, point) {

        //             // Apply the cell text
        //             renderer.text(
        //                     Highcharts.numberFormat(point.y, valueDecimals),
        //                     cellLeft + colWidth - cellPadding,
        //                     tableTop + (row + 2) * rowHeight - cellPadding
        //                 )
        //                 .attr({
        //                     align: 'right'
        //                 })
        //                 .add();

        //             // horizontal lines
        //             if (row == 0) {
        //                 Highcharts.tableLine( // top
        //                     renderer,
        //                     tableLeft,
        //                     tableTop + cellPadding,
        //                     cellLeft + colWidth,
        //                     tableTop + cellPadding
        //                 );
        //                 Highcharts.tableLine( // bottom
        //                     renderer,
        //                     tableLeft,
        //                     tableTop + (serie.data.length + 1) * rowHeight + cellPadding,
        //                     cellLeft + colWidth,
        //                     tableTop + (serie.data.length + 1) * rowHeight + cellPadding
        //                 );
        //             }
        //             // horizontal line
        //             Highcharts.tableLine(
        //                 renderer,
        //                 tableLeft,
        //                 tableTop + row * rowHeight + rowHeight + cellPadding,
        //                 cellLeft + colWidth,
        //                 tableTop + row * rowHeight + rowHeight + cellPadding
        //             );

        //         });

        //         // vertical lines
        //         if (i == 0) { // left table border
        //             Highcharts.tableLine(
        //                 renderer,
        //                 tableLeft,
        //                 tableTop + cellPadding,
        //                 tableLeft,
        //                 tableTop + (serie.data.length + 1) * rowHeight + cellPadding
        //             );
        //         }

        //         Highcharts.tableLine(
        //             renderer,
        //             cellLeft,
        //             tableTop + cellPadding,
        //             cellLeft,
        //             tableTop + (serie.data.length + 1) * rowHeight + cellPadding
        //         );

        //         if (i == series.length - 1) { // right table border

        //             Highcharts.tableLine(
        //                 renderer,
        //                 cellLeft + colWidth,
        //                 tableTop + cellPadding,
        //                 cellLeft + colWidth,
        //                 tableTop + (serie.data.length + 1) * rowHeight + cellPadding
        //             );
        //         }

        //     });


        // };
        // Highcharts.tableLine = function(renderer, x1, y1, x2, y2) {
        //     renderer.path(['M', x1, y1, 'L', x2, y2])
        //         .attr({
        //             'stroke': 'silver',
        //             'stroke-width': 1
        //         })
        //         .add();
        // }

        // var statisticChart = new Highcharts.Chart({

        //     chart: {
        //         renderTo: 'ushauri_statistics',
        //         events: {
        //             load: Highcharts.drawTable
        //         },
        //         borderWidth: 1
        //     },

        //     title: {
        //         text: 'Ushauri Statistics'
        //     },

        //     xAxis: {
        //         visible: false,
        //         categories: Ushauri_Statistics.map(function(x) {
        //             return x.facility_name;
        //         }),

        //     },

        //     yAxis: {
        //         visible: false
        //     },

        //     legend: {
        //         enabled: false
        //     },
        //     plotOptions: {
        //         series: {
        //             visible: false
        //         }
        //     },

        //     series: [{
        //         name: 'MFLCODE',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.mfl_code;
        //         }),
        //     }, {
        //         name: 'REGISTERED',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.registered_clients;
        //         }),

        //     }, {
        //         name: 'CONSENTED',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.consented;
        //         }),

        //     }, {
        //         name: 'APPOINTMENTS',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.appointments;
        //         }),

        //     }, {
        //         name: 'HONORED',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.honored;
        //         }),

        //     }, {
        //         name: 'MESSAGES',
        //         data: Ushauri_Statistics.map(function(x) {
        //             return x.messeges;
        //         }),

        //     }]
        // });



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
                },
                url: "{{ route('filter_uptake') }}",
                success: function(data) {
                    console.log(data);

                    $("#txcurr").html(data.txcur);
                    $("#registered_ushauri").html(data.registered);
                    $("#consented_sms").html(data.consented);
                    $("#scheduled_appointments").html(data.scheduledappointment);
                    $("#honored_appointments").html(data.honoredappointment);
                    Txcurr = parseInt(data.txcur)
                    Registered_Clients = parseInt(data.registered)
                    Consented_Clients = parseInt(data.consented)
                    Scheduled_App = parseInt(data.scheduledappointment)
                    Honored_App = parseInt(data.honoredappointment)

                    // $('#table').DataTable().ajax.reload();

                    uptakeCascade.series[0].setData([Txcurr, Registered_Clients, Consented_Clients, 0]);
                    appointmentuptakeCascade.series[0].setData([, Scheduled_App, , 0, Honored_App]);
                    // clientAge.series[0].setData([Client_to_nine, Client_to_fourteen, Client_to_nineteen, Client_to_twentyfour, Client_to_twentyfive_above, Client_unknown_age]);
                    // statisticChart.series[1].setData([data.]);
                    // statisticChart.series[2].setData([Consented_chart]);

                    Swal.close();


                }
            });
        });
    </script>





    <!-- end of col -->

    @endsection