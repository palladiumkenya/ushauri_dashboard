
@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="report/appointment_calendar">Appointment Calendar</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">
        <div class="card-body">


            <div class="table-responsive">
                <table id="appointments" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Honored</th>
                            <th>Unscheduled</th>
                            <th>Scheduled</th>
                            <th>Missed</th>
                            <th>Defaulted</th>
                            <th>IIT</th>

                        </tr>
                    </thead>

                    <tbody>
                        <?php $sum_defaulted = 0 ?>
                        <?php $sum_missed = 0 ?>
                        <?php $sum_ltfu = 0 ?>
                        <?php $sum_unscheduled = 0 ?>
                        <?php $sum_scheduled = 0 ?>
                        <?php $sum_honored = 0 ?>
                        @foreach ($result as $results)
                        <tr>

                        </tr>

                        <?php $sum_defaulted += substr_count($results->app_status, 'Defaulted') ?>
                        <?php $sum_missed += substr_count($results->app_status, 'Missed') ?>
                        <?php $sum_ltfu += substr_count($results->app_status, 'LTFU') ?>
                        <?php $sum_unscheduled += substr_count($results->visit_type, 'Un-Scheduled') ?>
                        @if($results->app_status == 'Notified' && $results->appntmnt_date > date("Y-m-d"))
                        <?php $sum_scheduled += substr_count($results->app_status, 'Notified') ?>
                        @elseif($results->app_status == 'Notified' && $results->appntmnt_date < date("Y-m-d"))
                        <?php $sum_honored += substr_count($results->app_status, 'Notified') ?>
                        @endif
                        <?php $sum_scheduled += substr_count($results->app_status, 'Booked') ?>

                        @endforeach

                        <tr>
                            <td> {{ $results->appntmnt_date}}</td>
                            <td> {{ $sum_honored}}</td>
                            <td> {{ $sum_unscheduled}}</td>
                            <td> {{ $sum_scheduled}}</td>
                            <td> {{ $sum_missed}}</td>
                            <td> {{ $sum_defaulted}}</td>
                            <td> {{ $sum_ltfu}}</td>
                        </tr>

                        <thead>
                            <tr><h4 class="card-title mb-3">Appointments Summary</h4> </tr>
                            <tr></tr>
                        </thead>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">
        <div class="card-body">
            <h4 class="card-title mb-3">Appointments List</h4>

            <div class="table-responsive">
                <table id="appointments_list" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>UPN</th>
                            <th>File No </th>
                            <th>Client Name</th>
                            <th>Phone No</th>
                            <th>Appointment Date</th>
                            <th>Appointment Type</th>
                            <th>Appointment Status</th>
                            <th>Consented</th>
                            <th>Message Status</th>
                            <th>Failure Reason</th>
                            <th>Clinic</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (count($result) > 0)
                        @foreach($result as $row)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$row->clinic_number}}</td>
                            <td> {{$row->file_no}}</td>
                            <td> {{$row->f_name.' '.$row->m_name.' '.$row->l_name}}</td>
                            <td> {{$row->phone_no}}</td>
                            <td> {{$row->appntmnt_date}}</td>
                            <td> {{$row->appointment_types}}</td>
                            @if($row->app_status == 'Notified' && $row->appntmnt_date > date("Y-m-d"))
                            <td> Booked </td>
                            @elseif($row->app_status == 'Notified' && $row->appntmnt_date < date("Y-m-d"))
                            <td> Honored </td>
                            @elseif($row->app_status == 'LTFU')
                            <td> IIT </td>
                            @else
                            <td> {{$row->app_status}}</td>
                            @endif
                            <td> {{$row->smsenable}}</td>
                            <td> {{$row->callback_status}}</td>
                            <td> {{$row->failure_reason}}</td>
                            <td> {{$row->clinic}}</td>
                            <td>
                                <input type="hidden" id="client_id" name="client_id" value="<?php echo $row->client_id; ?>" />
                                <input type="hidden" id="app_type_1" name="app_type_1" value="<?php echo $row->app_type_1; ?>" />
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

@endsection
@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    // multi column ordering
    $('#appointments_list').DataTable({
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
                title: 'Appointment List',
                filename: 'Appointment List'
            },
            {
                extend: 'csv',
                title: 'Appointment List',
                filename: 'Appointment List'
            },
            {
                extend: 'excel',
                title: 'Appointment List',
                filename: 'Appointment List'
            },
            {
                extend: 'pdf',
                title: 'Appointment List',
                filename: 'Appointment List'
            },
            {
                extend: 'print',
                title: 'Appointment List',
                filename: 'Appointment List'
            }
        ]
    });
</script>


@endsection
