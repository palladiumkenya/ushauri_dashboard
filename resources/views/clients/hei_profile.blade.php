@extends('layouts.master')
@section('page-css')


@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">HEI Profile</a></li>
        <li></li>
    </ul>
</div>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">HEI Profile</a>

    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">
        <div class="col-md-12 mb-4">
            <div class="card text-left">

                <div class="card-body">
                    <div style="margin-bottom:10px; ">
                        <div class="Search_Modal" style="display: inline;">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#heiModal"> <i class="fa fa-search"></i>
                                Search HEI
                            </button>
                        </div>

                        <!-- The Modal -->
                        <div class="modal" id="heiModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Search HEI</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form role="form" method="get" action="{{route('profile_search_hei')}}">
                                        {{ csrf_field() }}

                                        <!-- Modal body -->
                                        <div class="modal-body">

                                            <div class="search_field">
                                                <input type="text" class="hei_search form-control" id="hei_search" name="hei_search" placeholder="Please Enter HEI No : " />
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
                    </div>

                    <div class="container-fluid">
                        <!-- Start Page Content -->
                        <div class="row">

                            <!-- Column -->
                            <div class="col-lg-12">
                                <div class="card">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs profile-tab" role="tablist">
                                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                                        <li class="nav-item"> <a class="nav-link " data-toggle="tab" href="#appointments" role="tab">Appointments</a> </li>
                                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#outgoing" role="tab">Outgoing Messages</a> </li>
                                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#incoming" role="tab">Incoming Messages</a> </li>
                                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#wellness" role="tab">Wellness</a> </li>
                                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#outcomes" role="tab">Appointment Outcomes</a> </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane active " id="profile" role="tabpanel">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Full Name</th>
                                                                    <th>HEI No</th>
                                                                    <th>Gender</th>
                                                                    <th>DOB</th>
                                                                    <th>Mother CCC No</th>
                                                                    <th>Care Giver Name</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (count($hei_profile) > 0)
                                                                @foreach($hei_profile as $result)
                                                                <tr>
                                                                    <td> {{$loop->iteration }}</td>
                                                                    <td> {{$result->hei_name }}</td>
                                                                    <td> {{$result->hei_no}}</td>
                                                                    <td> {{$result->gender}}</td>
                                                                    <td> {{$result->hei_dob}}</td>
                                                                    <td> {{$result->clinic_number}}</td>
                                                                    <td> {{$result->caregiver_name}}</td>
                                                                </tr>
                                                                @endforeach
                                                                @endif
                                                            </tbody>

                                                        </table>

                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="tab-pane " id="appointments" role="tabpanel">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-4 col-xs-6 b-r"> <strong>Total Appointments : {{ $total_appointments }} </strong>
                                                        <br>
                                                        <p class="text-muted all_appointments"></p>
                                                    </div>
                                                    <div class="col-md-4 col-xs-6 b-r "> <strong>Future Appointments : {{ $future_appointment }}</strong>
                                                        <br>
                                                        <p class="text-muted current_appointments"></p>
                                                    </div>

                                                    <div class="col-md-4 col-xs-6"> <strong>Kept Appointments : {{ $kept_appointment }}</strong>
                                                        <br>
                                                        <p class="text-muted kept_appointments "></p>
                                                    </div>
                                                    <div class="col-md-4 col-xs-6"> <strong>Missed Appointments : {{ $missed_app }}</strong>
                                                        <br>
                                                        <p class="text-muted missed_appointments "></p>
                                                    </div>
                                                    <div class="col-md-4 col-xs-6 b-r"> <strong>Defaulted Appointments : {{ $defaulted_app }}</strong>
                                                        <br>
                                                        <p class="text-muted defaulted_appointments"></p>
                                                    </div>
                                                    <div class="col-md-4 col-xs-6 b-r "> <strong>LTFU Appointments : {{ $ltfu_app }}</strong>
                                                        <br>
                                                        <p class="text-muted LTFU_appointments"></p>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 col-xs-6 b-r "> <strong>No of Appointments by Type: </strong>
                                                        <ul class=" text-muted client_appointment_types">Re-Fill: {{ $refill_app }}</ul>
                                                        <ul class=" text-muted client_appointment_types">Clinical Review: {{ $clinical_app }}</ul>
                                                        <ul class=" text-muted client_appointment_types">Enhanced Adherence: {{ $adherence_app }}</ul>
                                                        <ul class=" text-muted client_appointment_types">Lab Investigation: {{ $lab_app }}</ul>
                                                        <ul class=" text-muted client_appointment_types">Viral Load: {{ $viral_app }}</ul>
                                                        <ul class=" text-muted client_appointment_types">Other: {{ $other_app }}</ul>
                                                    </div>
                                                </div>




                                            </div>
                                        </div>
                                        <!--second tab-->

                                        <div class="tab-pane" id="outgoing" role="tabpanel">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table id="outgoing_message_table" class="display table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>HEI Number</th>
                                                                    <th>Phone Number</th>
                                                                    <th>Message Type</th>
                                                                    <th>Message</th>
                                                                    <th>Sent On</th>


                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                                @if (count($outgoing_msg) > 0)
                                                                @foreach($outgoing_msg as $result)
                                                                <tr>
                                                                    <td> {{$loop->iteration }}</td>
                                                                    <td> {{$result->hei_no}}</td>
                                                                    <td> {{$result->destination}}</td>
                                                                    <td> {{$result->message_type}}</td>
                                                                    <td> {{$result->msg}}</td>
                                                                    <td> {{$result->created_at}}</td>

                                                                </tr>
                                                                @endforeach
                                                                @endif

                                                            </tbody>

                                                        </table>

                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                        <div class="tab-pane" id="incoming" role="tabpanel">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="incoming_messages_logs_div" id="incoming_messages_logs_div"></div>

                                                     
                                                </div>

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="wellness" role="tabpanel">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="wellness_messages_logs_div" id="wellness_messages_logs_div"></div>

                                                </div>

                                            </div>
                                        </div>



                                        <div class="tab-pane" id="outcomes" role="tabpanel">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="table-responsive">
                                                        <table id="appointment_outcome_table" class="display table table-striped table-bordered" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>HEI Number</th>
                                                                    <th>Appointment Date</th>
                                                                    <th>Appointment Type</th>
                                                                    <th>Tracer Name</th>
                                                                    <th>Outcome</th>
                                                                    <th>Final Outcome</th>


                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (count($appointment_outcome) > 0)
                                                                @foreach($appointment_outcome as $result)
                                                                <tr>
                                                                    <td> {{$loop->iteration }}</td>
                                                                    <td> {{$result->hei_no}}</td>
                                                                    <td> {{$result->appntmnt_date}}</td>
                                                                    <td> {{$result->app_type}}</td>
                                                                    <td> {{$result->tracer_name}}</td>
                                                                    <td> {{$result->outcome}}</td>
                                                                    <td> {{$result->final_outcome}}</td>

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
                            </div>
                            <!-- Column -->
                        </div>

                        <!-- End PAge Content -->
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

<script type="text/javascript">
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
                title: 'Clients Profile Registration Details',
                filename: 'Clients Details'
            },
            {
                extend: 'csv',
                title: 'Clients Profile Registration Details',
                filename: 'Clients Details'
            },
            {
                extend: 'excel',
                title: 'Clients Profile Registration Details',
                filename: 'Clients Details'
            },
            {
                extend: 'pdf',
                title: 'Clients Profile Registration Details',
                filename: 'Clients Details'
            },
            {
                extend: 'print',
                title: 'Clients Profile Registration Details',
                filename: 'Clients Details'
            }
        ]
    });

    $('#outgoing_message_table').DataTable({
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
                title: 'Clients Profile Outgoing Messages',
                filename: 'Clients Outgoing Messages'
            },
            {
                extend: 'csv',
                title: 'Clients Profile Outgoing Messages',
                filename: 'Clients Outgoing Messages'
            },
            {
                extend: 'excel',
                title: 'Clients Profile Outgoing Messages',
                filename: 'Clients Outgoing Messages'
            },
            {
                extend: 'pdf',
                title: 'Clients Profile Outgoing Messages',
                filename: 'Clients Outgoing Messages'
            },
            {
                extend: 'print',
                title: 'Clients Profile Outgoing Messages',
                filename: 'Clients Outgoing Messages'
            }
        ]
    });

    $('#appointment_outcome_table').DataTable({
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
                title: 'Clients Profile Appointment Outcome',
                filename: 'Clients Appointment Outcome'
            },
            {
                extend: 'csv',
                title: 'Clients Profile Appointment Outcome',
                filename: 'Clients Appointment Outcome'
            },
            {
                extend: 'excel',
                title: 'Clients Profile Appointment Outcome',
                filename: 'Clients Appointment Outcome'
            },
            {
                extend: 'pdf',
                title: 'Clients Profile Appointment Outcome',
                filename: 'Clients Appointment Outcome'
            },
            {
                extend: 'print',
                title: 'Clients Profile Appointment Outcome',
                filename: 'Clients Appointment Outcome'
            }
        ]
    });
</script>


@endsection