@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">Showing {{count($clients)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $clients->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>CCC Number</th>
                                                <th>Client Name</th>
                                                <th>Gender</th>
                                                <th>DOB</th>
                                                <th>Phone No</th>
                                                <th>Consent</th>
                                                <th>Date Created</th>
                                                <th>Facility Name</th>
                                                <th>MFL Code</th>
                                                <th>Partner Name</th>
                                                <th>County</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($clients) > 0)
                                                @foreach($clients as $client)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$client->clinic_number}}</td>
                                                        <td> {{$client->f_name}} {{$client->m_name}} {{$client->l_name}}</td>
                                                        <td>  {{$client->gender}}</td>
                                                        <td>  {{$client->dob}}</td>
                                                        <td>  {{$client->phone_no}}</td>
                                                        <td>  {{$client->smsenable}}</td>
                                                        <td>  {{date('d-m-Y', strtotime($client->created_at))}}</td>
                                                        <td>  {{$client->facility}}</td>
                                                        <td>  {{$client->code}}</td>
                                                        <td>  {{$client->partner}}</td>
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
            title: 'Clients List',
            filename: 'Clients List'
            },
            {
            extend: 'csv',
            exportOptions: {
                  columns: ':visible',
                  modifier:{
                    page:'all'
                  }
              },
            title: 'Clients List',
            filename: 'Clients List'
            }, 'excel', 'pdf', 'print'
        ]
    });</script>


@endsection
