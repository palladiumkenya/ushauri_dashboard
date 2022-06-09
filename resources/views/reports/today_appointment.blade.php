@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Today's Appointment</a></li>
                    <li></li>
                </ul>
            </div>



<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <! <h4 class="card-title mb-3">{{count($all_today_appointments)}} Today's Appointments List</h4>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>UPN</th>
                                                <th>Serial No</th>
                                                <th>Client Name</th>
                                                <th>Phone No</th>
                                                <th>Appointment Date</th>
                                                <th>Appointment Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_today_appointments) > 0)
                                                @foreach($all_today_appointments as $today_app)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$today_app->clinic_no}}</td>
                                                        <td>  {{$today_app->file_no}}</td>
                                                        <td>  {{$today_app->client_name}}</td>
                                                        <td>  {{$today_app->client_phone_no}}</td>
                                                        <td>  {{$today_app->appntmnt_date}}</td>
                                                        <td>  {{$today_app->appointment_type}}</td>
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

$(document).ready(function() {
            $('select[name="partner"]').on('change', function() {
                var partnerID = $(this).val();
                if (partnerID) {
                    $.ajax({
                        url: '/get_dashboard_counties/' + partnerID,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {


                            $('select[name="county"]').empty();
                            $('select[name="county"]').append('<option value="">Please Select County</option>');
                            $.each(data, function(key, value) {
                                $('select[name="county"]').append('<option value="' + key + '">' + value + '</option>');
                            });


                        }
                    });
                } else {
                    $('select[name="county"]').empty();
                }
            });
        });

        $(document).ready(function() {
            $('select[name="county"]').on('change', function() {
                var countyID = $(this).val();
                if (countyID) {
                    $.ajax({
                        url: '/get_dashboard_sub_counties/' + countyID,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {


                            $('select[name="subcounty"]').empty();
                            $('select[name="subcounty"]').append('<option value="">Please Select SubCounty</option>');
                            $.each(data, function(key, value) {
                                $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                            });


                        }
                    });
                } else {
                    $('select[name="subcounty"]').empty();
                }
            });
        });

        $(document).ready(function() {
            $('select[name="subcounty"]').on('change', function() {
                var subcountyID = $(this).val();
                if (subcountyID) {
                    $.ajax({
                        url: '/get_dashboard_facilities/' + subcountyID,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {


                            $('select[name="facility"]').empty();
                            $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                            $.each(data, function(key, value) {
                                $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                            });


                        }
                    });
                } else {
                    $('select[name="facility"]').empty();
                }
            });
        });
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
            title: 'Todays Appointments List',
            filename: 'Todays Appointments List'
            },
            {
            extend: 'csv',
            title: 'Todays Appointments List',
            filename: 'Todays Appointments List'
            },
            {
            extend: 'excel',
            title: 'Todays Appointments List',
            filename: 'Todays Appointments List'
            },
            {
            extend: 'pdf',
            title: 'Todays Appointments List',
            filename: 'Todays Appointments List'
            },
            {
            extend: 'print',
            title: 'Todays Appointments List',
            filename: 'Todays Appointments List'
            }
        ]
    });</script>


@endsection
