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

        <!-- @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')


        <div class="col-md-12">

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
            </div>
            @endif -->
            <div class="col-sm-3">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

</form>








<!-- end of col -->

@endsection

@section('page-js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    $('.partners').select2();
    $('.counties').select2();
    $('.subcounties').select2();

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
</script>


@endsection