@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">All Appointments</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                           <! <h4 class="card-title mb-3">{{count($all_appointments)}} Appointments List</h4>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                @if (env('INSTANCE') === 'UshauriDOD')
                                                <th>KDOD Number</th>
                                                <th>Service No</th>
                                                @else
                                                <th>Clinic Number</th>
                                                <th>Serial No</th>
                                                @endif
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Last Name</th>
                                                <th>Phone No</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Type</th>
                                                <th>Status</th>
                                                <th>Enrollment Date</th>
                                                <th>ART Date</th>
                                                <th>Date Added</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_appointments) > 0)
                                                @foreach($all_appointments as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->file_no}}</td>
                                                        <td>  {{$result->f_name}}</td>
                                                        <td>  {{$result->m_name}}</td>
                                                        <td>  {{$result->l_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->appntmnt_date}}</td>
                                                        <td>  {{$result->app_type_1}}</td>
                                                        <td>  {{$result->status}}</td>
                                                        <td>  {{$result->enrollment_date}}</td>
                                                        <td>  {{$result->art_date}}</td>
                                                        <td>  {{$result->created_at}}</td>
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
        "responsive":true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [
            {
            extend: 'copy',
            title: 'Appointments List',
            filename: 'Appointments List'
            },
            {
            extend: 'csv',
            title: 'Appointments List',
            filename: 'Appointments List'
            },
            {
            extend: 'excel',
            title: 'Appointments List',
            filename: 'Appointments List'
            },
            {
            extend: 'pdf',
            title: 'Appointments List',
            filename: 'Appointments List'
            },
            {
            extend: 'print',
            title: 'Appointments List',
            filename: 'Appointments List'
            }
        ]
    });</script>


@endsection
