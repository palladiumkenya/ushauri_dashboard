@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
    <ul>
        <li><a href="">Appointment Reschedule Requests</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <div style="margin-bottom:10px; ">

            </div>
            <div class="col-md-12" style="margin-top:10px; ">

            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Client Name</th>
                            <th>CCC Number</th>
                            <th>Appointment Date</th>
                            <th>Proposed Date</th>
                            <th>Reason For Reschedule</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($reschedule) > 0)
                        @foreach($reschedule as $result)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{ ucwords($result->f_name)}} {{ ucwords($result->m_name)}} {{ ucwords($result->l_name)}}</td>
                            <td> {{$result->clinic_number}}</td>
                            <td> {{$result->appntmnt_date}}</td>
                            <td> {{$result->proposed_date}}</td>
                            <td> {{$result->reason}}</td>

                            <td>
                                <button onclick="approveModal({{ $result }});" data-toggle="modal" data-target="#approveModal" type="button" class="btn btn-primary btn-sm">Approve</button>
                                <!-- <button onclick="rejectModal({{ $result }});"  data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button> -->
                                <button onclick="rejectModal({{ $result }});" data-toggle="modal" data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button>

                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>
<!-- end of col -->

<div id="approveModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Appointment Reschedule Approval</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('approve-reschedule')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="appointment_id" id="appointment_id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Appointment Date</label>
                                        <input type="text" class="form-control" id="appntmnt_date" name="appntmnt_date" placeholder="Appointment Date" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Proposed Appointment Date</label>
                                        <input type="text" class="form-control" id="proposed_date" name="proposed_date" placeholder="Proposed Date" readonly />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Approve</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<div id="rejectModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Appointment Reschedule Rejection</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('reject-reschedule')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="appointment_id" id="reject_appointment_id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="reject_clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Appointment Date</label>
                                        <input type="text" class="form-control" id="reject_appntmnt_date" name="appntmnt_date" placeholder="Appointment Date" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Proposed Appointment Date</label>
                                        <input type="text" class="form-control" id="reject_proposed_date" name="proposed_date" placeholder="Proposed Date" readonly />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Confirm Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

@endsection

@section('page-js')


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

<script type="text/javascript">
    function approveModal(result) {

        $('#clinic_number').val(result.clinic_number);
        $('#appntmnt_date').val(result.appntmnt_date);
        $('#proposed_date').val(result.proposed_date);
        $('#appointment_id').val(result.appointment_id);

    }

    function rejectModal(result) {

        $('#reject_clinic_number').val(result.clinic_number);
        $('#reject_appntmnt_date').val(result.appntmnt_date);
        $('#reject_proposed_date').val(result.proposed_date);
        $('#reject_appointment_id').val(result.appointment_id);
    }


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
                extend: 'copy'
            },
            {
                extend: 'csv'
            },
            {
                extend: 'excel'
            },
            {
                extend: 'pdf'
            },
            {
                extend: 'print'
            }
        ]
    });
</script>


@endsection