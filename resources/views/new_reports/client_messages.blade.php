@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="breadcrumb">
    <ul>
        <li><a href="">Client Messages</a></li>
        <li></li>
    </ul>
</div>
<form role="form" method="get" action="{{route('client_message')}}">
    {{ csrf_field() }}
    <div class="row">


        <div class='col'>
            <div class="form-group">
                <div class="input-group">
                    <div class="col-md-2">
                        <label for="firstName1">From</label>
                    </div>
                    <div class="col-md-10">
                        <input type="date" id="date_from" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="date_from" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="input-group-append">

                    </div>
                </div>
            </div>
        </div>

        <div class='col'>
            <div class="form-group">
                <div class="input-group">
                    <div class="col-md-2">
                        <label for="firstName1">To</label>
                    </div>
                    <div class="col-md-10">

                        <input type="date" id="date_to" class="form-control" placeholder="YYYY-mm-dd" name="date_to" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="input-group-append">

                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-3">
            <div class="form-group">

                <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                    Filter</button>
            </div>
        </div>
    </div>

</form>


<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">Messages Period</h4>
            <div class="col-md-12" style="margin-top:10px; ">
                <p><b> From: </b> {{ $selected_from }} <b> To: </b> {{ $selected_to }} </p>
            </div>
            <h4 class="card-title mb-3">Showing {{count($client_messages)}}</h4>
            <div class="col-md-12" style="margin-top:10px; ">

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
                            <td> {{$client->clinic_number}}</td>
                            <td> {{$client->f_name}} {{$client->m_name}} {{$client->l_name}}</td>
                            <td> {{$client->gender}}</td>
                            <td> {{$client->language}}</td>
                            <td> {{$client->phone_no}}</td>
                            <td> {{$client->msg}}</td>
                            <td> {{$client->callback_status}}</td>
                            <td> {{$client->failure_reason}}</td>
                            <td> {{date('d-m-Y', strtotime($client->updated_at))}}</td>
                            <td> {{$client->appointment_date}}</td>
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
        "responsive": true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                title: 'Clients Messages',
                filename: 'Clients Messages'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Clients Messages',
                filename: 'Clients Messages'
            }, 'excel', 'pdf', 'print'
        ]
    });
</script>


@endsection