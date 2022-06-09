@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Client Transfers</a></li>
                    <li></li>
                </ul>
            </div>

<!--  -->

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                         <h4 class="card-title mb-3">Transfer Out Client List</h4>
                         <h4 class="card-title mb-3">Showing {{count($all_transfer_clients)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $all_transfer_clients->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="transfer_out_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>UPN</th>
                                                <th>Serial No</th>
                                                <th>Client Name</th>
                                                <th>Phone No</th>
                                                <th>DOB</th>
                                                <th>Type</th>
                                                <th>Condition</th>
                                                <th>Previous Clinic</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_transfer_clients) > 0)
                                                @foreach($all_transfer_clients as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->file_no}}</td>
                                                        <td>  {{$result->full_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->dob}}</td>
                                                        <td>  {{$result->name}}</td>
                                                        <td>  {{$result->client_status}}</td>
                                                        <td>  {{$result->clinic_previous}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>

                                    </table>

                                </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                         <h4 class="card-title mb-3">Transfer In Client List</h4>
                         <h4 class="card-title mb-3">Showing {{count($all_transfer_in)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $all_transfer_in->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="transfer_in_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>UPN</th>
                                                <th>Serial No</th>
                                                <th>Client Name</th>
                                                <th>Phone No</th>
                                                <th>DOB</th>
                                                <th>Type</th>
                                                <th>Condition</th>
                                                <th>Previous Clinic</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_transfer_in) > 0)
                                                @foreach($all_transfer_in as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->file_no}}</td>
                                                        <td>  {{$result->full_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->dob}}</td>
                                                        <td>  {{$result->name}}</td>
                                                        <td>  {{$result->client_status}}</td>
                                                        <td>  {{$result->clinic_previous}}</td>
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
   $('#transfer_out_table').DataTable({
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
            title: 'Transfer Out Client List',
            filename: 'Transfer Out Client List'
            },
            {
            extend: 'csv',
            title: 'Transfer Out Client List',
            filename: 'Transfer Out Client List'
            },
            {
            extend: 'excel',
            title: 'Transfer Out Client List',
            filename: 'Transfer Out Client List'
            },
            {
            extend: 'pdf',
            title: 'Transfer Out Client List',
            filename: 'Transfer Out Client List'
            },
            {
            extend: 'print',
            title: 'Transfer Out Client List',
            filename: 'Transfer Out Client List'
            }
        ]
    });
    $('#transfer_in_table').DataTable({
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
            title: 'Transfer In Client List',
            filename: 'Transfer In Client List'
            },
            {
            extend: 'csv',
            title: 'Transfer In Client List',
            filename: 'Transfer In Client List'
            },
            {
            extend: 'excel',
            title: 'Transfer In Client List',
            filename: 'Transfer In Client List'
            },
            {
            extend: 'pdf',
            title: 'Transfer In Client List',
            filename: 'Transfer In Client List'
            },
            {
            extend: 'print',
            title: 'Transfer In Client List',
            filename: 'Transfer In Client List'
            }
        ]
    });
    </script>


@endsection
