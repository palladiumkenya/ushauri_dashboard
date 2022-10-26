@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection
@section('main-content')

<div class="breadcrumb">
    <ul>
        <li><a>Dashboard</a></li>
        <li><a>Appointment Register</a></li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>
<div class="col-md-12">


    <div class="row">
        <div style="margin-bottom:10px; ">
            <a type="button" href="{{route('new_client')}}" class="btn btn-primary btn-md pull-right">Add Client</a>
        </div>
        <div class="col-lg-1 style=" margin-bottom:10px; ">
        </div>

    </div>

    <div class=" row">
            <div class="col-lg-6 col-md-12">
                <div class="card mb-4">
                    <div class="panel-heading">
                        <i class="icon-table"></i>{{count($today_appointment)}} Today's Appointments
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="today_appointment_table" class="display table table-striped table-bordered" style="width:50%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>UPN</th>
                                        <th>Serial No</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Type</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($today_appointment) > 0)
                                    @foreach($today_appointment as $result)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{$result->clinic_no}}</td>
                                        <td> {{$result->file_no}}</td>
                                        <td> {{$result->client_name}}</td>
                                        <td> {{$result->client_phone_no}}</td>
                                        <td> {{$result->appntmnt_date}}</td>
                                        <td> {{$result->appointment_type}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="card mb-4">
                    <div class="panel-heading">
                        <i class="icon-table"></i> {{count($missed_appoitment)}} Missed Appointments
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="missed_table" class="display table table-striped table-bordered" style="width:50%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>UPN</th>
                                        <th>Serial No</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Type</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @if (count($missed_appoitment) > 0)
                                    @foreach($missed_appoitment as $result)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{$result->clinic_number}}</td>
                                        <td> {{$result->file_no}}</td>
                                        <td> {{$result->full_name}}</td>
                                        <td> {{$result->phone_no}}</td>
                                        <td> {{$result->appntmnt_date}}</td>
                                        <td> {{$result->name}}</td>
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

        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card mb-4">
                    <div class="panel-heading">
                        <i class="icon-table"></i> {{count($defaulted_appoitment)}} Defaulted Appointments
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="defaulted_table" class="display table table-striped table-bordered" style="width:50%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>UPN</th>
                                        <th>Serial No</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Type</th>


                                    </tr>
                                </thead>
                                <tbody>

                                    @if (count($defaulted_appoitment) > 0)
                                    @foreach($defaulted_appoitment as $result)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{$result->clinic_number}}</td>
                                        <td> {{$result->file_no}}</td>
                                        <td> {{$result->full_name}}</td>
                                        <td> {{$result->phone_no}}</td>
                                        <td> {{$result->appntmnt_date}}</td>
                                        <td> {{$result->name}}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div class="card mb-4">
                    <div class="panel-heading">
                        <i class="icon-table"></i>{{count($ltfu_appoitment)}} LTFU Appointments
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ltfu_table" class="display table table-striped table-bordered" style="width:50%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>UPN</th>
                                        <th>Serial No</th>
                                        <th>Client Name</th>
                                        <th>Phone No</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Type</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @if (count($ltfu_appoitment) > 0)
                                    @foreach($ltfu_appoitment as $result)
                                    <tr>
                                        <td> {{ $loop->iteration }}</td>
                                        <td> {{$result->clinic_number}}</td>
                                        <td> {{$result->file_no}}</td>
                                        <td> {{$result->full_name}}</td>
                                        <td> {{$result->phone_no}}</td>
                                        <td> {{$result->appntmnt_date}}</td>
                                        <td> {{$result->name}}</td>
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

    <div id="FirstModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="col-md-12">

                        <form role="form" method="post" action="{{route('changepass')}}">
                            {{ csrf_field() }}
                            <div class="row">
                                <input type="hidden" name="id" value="{{Auth::user()->id}}">

                                <div class="form-group col-md-12">
                                    <label for="new_password" class="col-sm-2 control-label ">New Password </label>
                                    <div class="col-sm-10">
                                        <input type="password" pattern=".{6,20}" required title="password requires more than 6 characters" class="form-control new_password password" name="new_password" id="new_password" placeholder="New Password... ">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="confirm_new_password" class="col-sm-2 control-label">Confirm Password
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="password" pattern=".{6,20}" required title="password requires more than 6 characters" class="form-control confirm_new_password password" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm Password... ">
                                    </div>
                                </div>
                            </div>
                            <div class="btn_div" style="display: none;">
                                <button type="submit" class="btn btn-info pull-right">Change Password</button>

                            </div>
                        </form>

                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    @endsection

    @section('page-js')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js"> </script>
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

    <script type="text/javascript">
        st = '{!! Auth::user()->first_login !!}';
        if (st == 'Yes') {
            $('#FirstModal').modal('show');
        }
        $(".password").keyup(function() {
            var password = $("#new_password").val();
            var password2 = $("#confirm_new_password").val();
            if (password == password2) {
                $(".btn_div").show();
            } else {
                $(".btn_div").hide();
            }
        });

        $('#today_appointment_table').DataTable({
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
                    title: 'Todays Appointments',
                    filename: 'Todays Appointment'
                },
                {
                    extend: 'csv',
                    title: 'Todays Appointments',
                    filename: 'Todays Appointment'
                },
                {
                    extend: 'excel',
                    title: 'Todays Appointments',
                    filename: 'Todays Appointment'
                },
                {
                    extend: 'pdf',
                    title: 'Todays Appointments',
                    filename: 'Todays Appointment'
                },
                {
                    extend: 'print',
                    title: 'Todays Appointments',
                    filename: 'Todays Appointment'
                }

            ]
        });
        $('#missed_table').DataTable({
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
                    title: 'Active Missed Appointments',
                    filename: 'Active Missed Appointments'
                },
                {
                    extend: 'csv',
                    title: 'Active Missed Appointments',
                    filename: 'Active Missed Appointments'
                },
                {
                    extend: 'excel',
                    title: 'Active Missed Appointments',
                    filename: 'Active Missed Appointments'
                },
                {
                    extend: 'pdf',
                    title: 'Active Missed Appointments',
                    filename: 'Active Missed Appointments'
                },
                {
                    extend: 'print',
                    title: 'Active Missed Appointments',
                    filename: 'Active Missed Appointments'
                }
            ]
        });
        $('#defaulted_table').DataTable({
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
                    title: 'Active Defaulted Appointments',
                    filename: 'Active Defaulted Appointments'
                },
                {
                    extend: 'csv',
                    title: 'Active Defaulted Appointments',
                    filename: 'Active Defaulted Appointments'
                },
                {
                    extend: 'excel',
                    title: 'Active Defaulted Appointments',
                    filename: 'Active Defaulted Appointments'
                },
                {
                    extend: 'pdf',
                    title: 'Active Defaulted Appointments',
                    filename: 'Active Defaulted Appointments'
                },
                {
                    extend: 'print',
                    title: 'Active Defaulted Appointments',
                    filename: 'Active Defaulted Appointments'
                }
            ]
        });
        // multi column ordering
        $('#ltfu_table').DataTable({
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
                    title: 'Active LTFU Appointments',
                    filename: 'Active LTFU Appointments'
                },
                {
                    extend: 'csv',
                    title: 'Active LTFU Appointments',
                    filename: 'Active LTFU Appointments'
                },
                {
                    extend: 'excel',
                    title: 'Active LTFU Appointments',
                    filename: 'Active LTFU Appointments'
                },
                {
                    extend: 'pdf',
                    title: 'Active LTFU Appointments',
                    filename: 'Active LTFU Appointments'
                },
                {
                    extend: 'print',
                    title: 'Active LTFU Appointments',
                    filename: 'Active LTFU Appointments'
                }
            ]
        });
    </script>

    @endsection