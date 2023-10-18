@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Tracing Clients</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <! <h4 class="card-title mb-3">{{count($tracer_client_list)}} List of Clients Assigned To Tracers</h4>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                @if (env('INSTANCE') === 'UshauriDOD')
                                                <th>KDOD No</th>
                                                @else
                                                <th>UPN</th>
                                                @endif
                                                <th>Appointment Date</th>
                                                <th>Appointment Type</th>
                                                <th>Appointment Status</th>
                                                <th>Client Phone No</th>
                                                <th>Clinic</th>
                                                <th>Tracer Name</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($tracer_client_list) > 0)
                                                @foreach($tracer_client_list as $tracer)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$tracer->clinic_number}}</td>
                                                        <td>  {{$tracer->appntmnt_date}}</td>
                                                        <td>  {{$tracer->app_type}}</td>
                                                        <td>  {{$tracer->app_status}}</td>
                                                        <td>  {{$tracer->client_contact}}</td>
                                                        <td>  {{$tracer->clinic}}</td>
                                                        <td>  {{$tracer->tracer_name}}</td>

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
            title: 'List of Clients Assigned To Tracers',
            filename: 'List of Clients Assigned To Tracers'
            },
            {
            extend: 'csv',
            title: 'List of Clients Assigned To Tracers',
            filename: 'List of Clients Assigned To Tracers'
            },
            {
            extend: 'excel',
            title: 'List of Clients Assigned To Tracers',
            filename: 'List of Clients Assigned To Tracers'
            },
            {
            extend: 'pdf',
            title: 'List of Clients Assigned To Tracers',
            filename: 'List of Clients Assigned To Tracers'
            },
            {
            extend: 'print',
            title: 'List of Clients Assigned To Tracers',
            filename: 'List of Clients Assigned To Tracers'
            }
        ]
    });</script>


@endsection
