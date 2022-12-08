@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Future Appointments</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                           <! <h4 class="card-title mb-3">{{count($all_future_appointments)}} Future Appointments List</h4>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Clinic Number</th>
                                                <th>UPI Number</th>
                                                <th>Serial No</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Last Name</th>
                                                <th>Phone No</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Type</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_future_appointments) > 0)
                                                @foreach($all_future_appointments as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->upi_no}}</td>
                                                        <td>  {{$result->file_no}}</td>
                                                        <td>  {{$result->f_name}}</td>
                                                        <td>  {{$result->m_name}}</td>
                                                        <td>  {{$result->l_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->appntmnt_date}}</td>
                                                        <td>  {{$result->app_type}}</td>
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
            title: 'Future Appointments',
            filename: 'Future Appointments'
            },
            {
            extend: 'csv',
            title: 'Future Appointments',
            filename: 'Future Appointments'
            },
            {
            extend: 'excel',
            title: 'Future Appointments',
            filename: 'Future Appointments'
            },
            {
            extend: 'pdf',
            title: 'Future Appointments',
            filename: 'Future Appointments'
            },
            {
            extend: 'print',
            title: 'Future Appointments',
            filename: 'Future Appointments'
            }
        ]
    });</script>


@endsection
