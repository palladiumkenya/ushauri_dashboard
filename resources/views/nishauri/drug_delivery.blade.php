@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
    <ul>
        <li><a href="">Drug Delivery Requests</a></li>
        <li></li>
    </ul>
</div>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#pending">Pending Requests</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#approved">Approved Requests</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#fullfilled">Fullfilled Requests</a>
    </li>
</ul>

<div class="tab-content">
    <div id="pending" class="container tab-pane active"><br>
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
                                    <th>CCC Number</th>
                                    <th>Appointment Date</th>
                                    <th>Request Mode</th>
                                    <th>Delivery Method</th>
                                    <th>Delivery Person</th>
                                    <th>Delivery Person Contact</th>
                                    <th>Pick Up Time</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($drug_delivery) > 0)
                                @foreach($drug_delivery as $result)
                                <tr>
                                    <td> {{ $loop->iteration }}</td>
                                    <td> {{$result->clinic_number}}</td>
                                    <td> {{$result->appntmnt_date}}</td>
                                    <td> {{$result->mode}}</td>
                                    <td> {{$result->delivery_method}}</td>
                                    <td> {{$result->delivery_person}}</td>
                                    <td> {{$result->delivery_person_contact}}</td>
                                    <td> {{$result->delivery_pickup_time}}</td>
                                    <td> {{$result->status}}</td>

                                    <td>
                                        <button onclick="approveModal({{ $result }});" data-toggle="modal" data-target="#approveModal" type="button" class="btn btn-primary btn-sm">Approve</button>
                                        <!-- <button onclick="rejectModal({{ $result }});"  data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button> -->
                                        <!-- <button onclick="rejectModal({{ $result }});" data-toggle="modal" data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button> -->

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
    </div>
    <div id="approved" class="container tab-pane fade"><br>
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
                                    <th>CCC Number</th>
                                    <th>Appointment Date</th>
                                    <th>Request Mode</th>
                                    <th>Delivery Method</th>
                                    <th>Delivery Person</th>
                                    <th>Delivery Person Contact</th>
                                    <th>Pick Up Time</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($drug_dispatch) > 0)
                                @foreach($drug_dispatch as $result)
                                <tr>
                                    <td> {{ $loop->iteration }}</td>
                                    <td> {{$result->clinic_number}}</td>
                                    <td> {{$result->appntmnt_date}}</td>
                                    <td> {{$result->mode}}</td>
                                    <td> {{$result->delivery_method}}</td>
                                    <td> {{$result->delivery_person}}</td>
                                    <td> {{$result->delivery_person_contact}}</td>
                                    <td> {{$result->delivery_pickup_time}}</td>
                                    <td> {{$result->status}}</td>

                                    <td>
                                        <button onclick="dispatchModal({{ $result }});" data-toggle="modal" data-target="#dispatchModal" type="button" class="btn btn-primary btn-sm">Dispatch</button>
                                        <!-- <button onclick="rejectModal({{ $result }});"  data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button> -->
                                        <!-- <button onclick="rejectModal({{ $result }});" data-toggle="modal" data-target="#rejectModal" type="button" class="btn btn-danger btn-sm">Reject</button> -->

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
    </div>
    <div id="fullfilled" class="container tab-pane fade"><br>
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
                                    <th>CCC Number</th>
                                    <th>Appointment Date</th>
                                    <th>Request Mode</th>
                                    <th>Delivery Method</th>
                                    <th>Delivery Person</th>
                                    <th>Delivery Person Contact</th>
                                    <th>Pick Up Time</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($drug_fullfilled) > 0)
                                @foreach($drug_fullfilled as $result)
                                <tr>
                                    <td> {{ $loop->iteration }}</td>
                                    <td> {{$result->clinic_number}}</td>
                                    <td> {{$result->appntmnt_date}}</td>
                                    <td> {{$result->mode}}</td>
                                    <td> {{$result->delivery_method}}</td>
                                    <td> {{$result->delivery_person}}</td>
                                    <td> {{$result->delivery_person_contact}}</td>
                                    <td> {{$result->delivery_pickup_time}}</td>
                                    <td> {{$result->status}}</td>


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
<!-- end of col -->

<div id="approveModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Drug Delivery Approval</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('approve-delivery')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="appointment_id" id="appointment_id">
                                    <input type="hidden" name="order_id" id="order_id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Appointment Date</label>
                                        <input type="text" class="form-control" id="appntmnt_date" name="appntmnt_date" placeholder="Appointment Date" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Pick Up Time</label>
                                        <input type="text" class="form-control" id="delivery_pickup_time" name="delivery_pickup_time" placeholder="Pick Up Time" readonly />
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
<div id="dispatchModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Drug Delivery Dispatch</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('approve-dispatch')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="appointment_id" id="dispatch_appointment_id">
                                    <input type="hidden" name="order_id" id="dispatch_order_id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="dispatch_clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Appointment Date</label>
                                        <input type="text" class="form-control" id="dispatch_appntmnt_date" name="appntmnt_date" placeholder="Appointment Date" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Pick Up Time</label>
                                        <input type="text" class="form-control" id="dispatch_delivery_pickup_time" name="delivery_pickup_time" placeholder="Pick Up Time" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Dispatch Notes</label>
                                        <input type="text" class="form-control" id="dispatch_notes" name="dispatch_notes" placeholder="Dispatch Notes" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Dispatch</button>
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
        $('#delivery_pickup_time').val(result.delivery_pickup_time);
        $('#appointment_id').val(result.appointment_id);
        $('#order_id').val(result.order_id);

    }

    function dispatchModal(result) {

        $('#dispatch_clinic_number').val(result.clinic_number);
        $('#dispatch_appntmnt_date').val(result.appntmnt_date);
        $('#dispatch_delivery_pickup_time').val(result.delivery_pickup_time);
        $('#dispatch_appointment_id').val(result.appointment_id);
        $('#dispatch_order_id').val(result.order_id);
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