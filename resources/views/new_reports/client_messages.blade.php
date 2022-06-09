@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">Showing {{count($client_messages)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $client_messages->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>CCC Number</th>
                                                <th>Client Name</th>
                                                <th>Gender</th>
                                                <th>Language</th>
                                                <th>Phone No</th>
                                                <th>Message</th>
                                                <th>Message Status</th>
                                                <th>Failed Reason</th>
                                                <th>Date Created</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Type</th>
                                                <th>Appointment Status</th>
                                                <th>Facility Name</th>
                                                <th>MFL Code</th>
                                                <th>Partner Name</th>
                                                <th>Sub County</th>
                                                <th>County</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($client_messages) > 0)
                                                @foreach($client_messages as $client)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$client->clinic_number}}</td>
                                                        <td> {{$client->f_name}} {{$client->m_name}} {{$client->l_name}}</td>
                                                        <td>  {{$client->gender}}</td>
                                                        <td>  {{$client->language}}</td>
                                                        <td>  {{$client->phone_no}}</td>
                                                        <td>  {{$client->msg}}</td>
                                                        <td>  {{$client->callback_status}}</td>
                                                        <td>  {{$client->failure_reason}}</td>
                                                        <td>  {{date('d-m-Y', strtotime($client->updated_at))}}</td>
                                                        <td>  {{$client->appointment_date}}</td>
                                                        <td> {{$client->app_type}}</td>
                                                        <td> {{$client->app_status}}</td>
                                                        <td> {{$client->facility}}</td>
                                                        <td> {{$client->code}}</td>
                                                        <td> {{$client->partner}}</td>
                                                        <td> {{$client->subcounty}}</td>
                                                        <td> {{$client->county}}</td>

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
            title: 'Clients Messages',
            filename: 'Clients Messages'
            },
            {
            extend: 'csv',
            exportOptions: {
                  columns: ':visible',
                  modifier:{
                    page:'all'
                  }
              },
            title: 'Clients Messages',
            filename: 'Clients Messages'
            }, 'excel', 'pdf', 'print'
        ]
    });</script>


@endsection
