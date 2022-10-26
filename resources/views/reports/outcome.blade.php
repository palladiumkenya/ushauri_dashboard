@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">Tracing Outcome</a></li>
        <li></li>
    </ul>
</div>
<form role="form" method="get" action="{{route('tracing_outcome_filter')}}">
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

<!-- @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')


<div class="col-md-12">

    <form role="form" method="get" action="{{route('tracing_outcome_filter')}}" >
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <div class="form-group">

                    <select class="form-control filter_partner  input-rounded input-sm select2" id="partners" name="partner">
                        <option value="">Please select Partner</option>
                        @foreach ($all_partners as $partner => $value)
                        <option value="{{ $partner }}"> {{ $value }}</option>
                        @endforeach
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <select class="form-control county  input-rounded input-sm select2" id="counties" name="county">
                        <option value="">Please select County:</option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value="">Please Select Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">Loading , Please Wait ...</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Please select Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif -->

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <! <h4 class="card-title mb-3">Tracing Outcome Report Period</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    <p><b> From: </b> {{ $selected_from }} <b> To: </b> {{ $selected_to }} </p>
                </div>
                <div class="table-responsive" id="outcome_div">
                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Clinic Number</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>MFL Code</th>
                                <th>Facility</th>
                                <th>Partner</th>
                                <th>Sub County</th>
                                <th>County</th>
                                <th>Appointment Date</th>
                                <th>Date Came</th>
                                <th>Tracer Name</th>
                                <th>Days Defaulted</th>
                                <th>Tracing Date</th>
                                <th>Outcome</th>
                                <th>Final Outcome</th>
                                <th>Other Outcome</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count(array($outcome_report)) > 0)
                            @foreach($outcome_report as $outcome)
                            <tr>
                                <td> {{ $outcome->UPN }}</td>
                                <td> {{$outcome->Age}}</td>
                                <td> {{$outcome->Gender}}</td>
                                <td> {{$outcome->MFL}}</td>
                                <td> {{$outcome->Facility}}</td>
                                <td> {{$outcome->Partner}}</td>
                                <td> {{$outcome->Sub_County}}</td>
                                <td> {{$outcome->County}}</td>
                                <td> {{$outcome->Appointment_Date}}</td>
                                <td> {{$outcome->Date_Came}}</td>
                                <td> {{$outcome->Tracer}}</td>
                                <td> {{$outcome->Days_Defaulted}}</td>
                                <td> {{$outcome->Tracing_Date}}</td>
                                <td> {{$outcome->Outcome}}</td>
                                <td> {{$outcome->Final_Outcome}}</td>
                                <td> {{$outcome->Other_Outcome}}</td>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
</script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    // $('#outcome_div').hide();

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
        "responsive": true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                title: 'Outcome Report',
                filename: 'Outcome Report'
            },
            {
                extend: 'csv',
                title: 'Outcome Report',
                filename: 'Outcome Report'
            },
            {
                extend: 'excel',
                title: 'Outcome Report',
                filename: 'Outcome Report'
            },
            {
                extend: 'pdfHtml5',
                title: 'Outcome Report',
                orientation: 'landscape',
                filename: 'Outcome Report'
            },
            {
                extend: 'print',
                title: 'Outcome Report',
                filename: 'Outcome Report'
            }
        ]
    });
    //TRACING OUTCOME STARTS HERE ...
    var dataTable = $('#empTable').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': true, // Set false to Remove default Search Control
        'ajax': {
            'url': 'ajaxfile.php',
            'data': function(data) {
                // Read values
                var from_date = $('#date_from').val();
                var to_date = $('#date_to').val();

                // Append to data
                data.searchByFromdate = from_date;
                data.searchByTodate = to_date;
            }
        },
        'columns': [{
                data: 'emp_name'
            },
            {
                data: 'email'
            },
            {
                data: 'date_of_joining'
            },
            {
                data: 'salary'
            },
            {
                data: 'city'
            },
        ]
    });

    // Search button
    $('#btn_search').click(function() {
        dataTable.draw();
    });
</script>


@endsection