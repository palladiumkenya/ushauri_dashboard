@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Client Extract</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                         <h4 class="card-title mb-3">Clients Extract List</h4>
                         <form role="form" method="get"action="{{route('filter-clients-extract')}}">
                            {{ csrf_field() }}
                                <div class="row">

                                    <div class='col-sm-6'>
                                        <div class="form-group">
                                            <div class="input-group">
                                            <div class="col-md-4">
                                            <label for="firstName1">From</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="date" id="from" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="from" >
                                                </div>
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary"  type="button">
                                                        <i class="icon-regular i-Calendar-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='col-sm-6'>
                                        <div class="form-group">
                                            <div class="input-group">
                                            <div class="col-md-4">
                                            <label for="firstName1">To</label>
                                            </div>
                                            <div class="col-md-10">

                                                <input type="date" id="to" class="form-control" placeholder="YYYY-mm-dd" name="to" >
                                               </div>
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary"  type="button">
                                                        <i class="icon-regular i-Calendar-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Submit</button>
                            </form>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Clinic Number</th>
                                                <th>UPI Number</th>
                                                <th>Gender</th>
                                                <th>Group</th>
                                                <th>Marital Status</th>
                                                <th>Created At</th>
                                                <th>Month Year</th>
                                                <th>Consent</th>
                                                <th>Language</th>
                                                <th>Text Time</th>
                                                <th>Enrollment Date</th>
                                                <th>ART Date</th>
                                                <th>Partner</th>
                                                <th>County</th>
                                                <th>Sub County</th>
                                                <th>MFL Code</th>
                                                <th>Facility</th>
                                                <th>SMS Enable</th>

                                                <th>Wellness Enable</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($client_extract) > 0)
                                                @foreach($client_extract as $clients)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$clients->clinic_number}}</td>
                                                        <td>  {{$clients->upi_no}}</td>
                                                        <td>  {{$clients->gender}}</td>
                                                        <td>  {{$clients->group_name}}</td>
                                                        <td>  {{$clients->marital}}</td>
                                                        <td>  {{date('d-m-Y', strtotime($clients->created_at))}}</td>
                                                        <td>  {{$clients->month_year}}</td>
                                                        <td>  {{$clients->consented}}</td>
                                                        <td>  {{$clients->LANGUAGE}}</td>
                                                        <td>  {{$clients->txt_time}}</td>
                                                        <td>  {{$clients->enrollment_date}}</td>
                                                        <td>  {{$clients->art_date}}</td>
                                                        <td>  {{$clients->partner_name}}</td>
                                                        <td>  {{$clients->county}}</td>
                                                        <td>  {{$clients->sub_county}}</td>
                                                        <td>  {{$clients->mfl_code}}</td>
                                                        <td>  {{$clients->facility_name}}</td>
                                                        <td>  {{$clients->consented}}</td>

                                                        <td>  {{$clients->wellness_enable}}</td>
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
            title: 'Active LTFU Appointments',
            filename: 'Active LTFU Appointments'
            },
            {
            extend: 'csv',
            title: 'Clients Extract',
            filename: 'Clients Extract'
            },
            {
            extend: 'excel',
            title: 'Clients Extract',
            filename: 'Clients Extract'
            },
            {
            extend: 'pdf',
            title: 'Clients Extract',
            filename: 'Clients Extract'
            },
            {
            extend: 'print',
            title: 'Clients Extract',
            filename: 'Clients Extract'
            }
        ]
    });</script>


@endsection
