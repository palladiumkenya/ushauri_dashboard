@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Edit Appointment</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <div class="col-md-12" style="margin-top:10px; ">

            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Clinic Number</th>
                            <th>UPI Number</th>
                            <th>Appointment Date</th>
                            <th>Appointment Type</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($all_future_apps) > 0)
                        @foreach($all_future_apps as $appointment)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$appointment->clinic_number}}</td>
                            <td> {{$appointment->upi_no}}</td>
                            <td> {{$appointment->appntmnt_date}}</td>
                            <td> {{$appointment->app_type}}</td>

                            <td>
                                <button onclick="editApp({{ $appointment }});" data-toggle="modal" data-target="#editApp" type="button" class="btn btn-primary btn-sm">Edit</button>
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

<div id="editApp" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit Appointment Detail</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('editappointment')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class='col-sm-6'>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <label for="firstName1">Appointment Date</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="date" required="" id="appntmnt_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="appntmnt_date">
                                                </div>
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary" type="button">
                                                        <i class="icon-regular i-Calendar-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="picker1">Appointment Type</label>
                                        <select id="app_type" name="app_type" class="form-control" required="">
                                            <option>Select</option>

                                            @foreach($all_app_types as $type)
                                            <option value="{{$type->id }}">{{ ucwords($type->name) }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Reason For Editing</label>
                                        <input type="text" class="form-control" id="reason" name="reason" placeholder="Please Enter A Reason" />
                                    </div>


                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Submit</button>
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

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    function editApp(appointment) {

        $('#clinic_number').val(appointment.clinic_number);
        $('#appntmnt_date').val(appointment.appntmnt_date);
        $('#app_type').val(appointment.app_type);
        $('#id').val(appointment.id);

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
        buttons: [
            {
            extend: 'copy',
            title: 'Future Appointments',
            filename: 'Future Appointments'
            },
            {
            extend: 'csv',
            title: 'Future Appointments Edit List',
            filename: 'Future Appointments Edit List'
            },
            {
            extend: 'excel',
            title: 'Future Appointments Edit List',
            filename: 'Future Appointments Edit List'
            },
            {
            extend: 'pdf',
            title: 'Future Appointments Edit List',
            filename: 'Future Appointments Edit List'
            },
            {
            extend: 'print',
            title: 'Future Appointments Edit List',
            filename: 'Future Appointments Edit List'
            }
        ]
    });
</script>


@endsection