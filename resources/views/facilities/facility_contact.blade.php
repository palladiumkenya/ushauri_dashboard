@extends('layouts.master')
@section('before-css')


@endsection

@section('main-content')
@include('sweetalert::alert')

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3 font-weight-bold" style="font-size: 15px;">Capture facility contacts</div>
                <form role="form" method="post" action="{{route('facility-contact')}}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-2 form-group mb-3">
                            <label for="ccc_phone" class="font-weight-bold" style="font-size: 15px;">CCC Phone No.</label>
                        </div>

                        <div class="col-md-10 form-group mb-3">
                            <input type="text" required="" name="ccc_phone" placeholder="Phone No should be 10 Digits " value="{{ old('ccc_phone') }}" id="ccc_phone" class="input-rounded input-sm form-control phone_no" />
                            <input type="hidden" name="mflcode" value="{{ $mflcode }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 form-group mb-3">
                            <label for="pmtct_phone" class="font-weight-bold" style="font-size: 15px;">PMTCT Phone No.</label>
                        </div>

                        <div class="col-md-10 form-group mb-3">
                            <input type="text" required="" name="pmtct_phone"  placeholder="Phone No should be 10 Digits " value="{{ old('pmtct_phone') }}" id="pmtct_phone" class="input-rounded input-sm form-control phone_no" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 form-group mb-3">
                            &nbsp;
                        </div>
                        <div class="col-md-10 form-group mb-3">
                            <button type="submit" class="btn btn-block btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@endsection
@section('page-js')

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="add_access_level"]').on('change', function() {
            var AccessID = $(this).val();
            if (AccessID) {
                $.ajax({
                    url: '/get_roles/' + AccessID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="rolename"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="rolename"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="rolename"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var CountyID = $(this).val();
            if (CountyID) {
                $.ajax({
                    url: '/get_sub_counties/' + CountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="sub_county"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="sub_county"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="sub_county"]').empty();
            }
        });
    });

    $("#add_access_level").change(function() {
        if ($(this).val() != "") {
            var select = $(this).attr("id");
            var value = $(this).val();
            var dependant = $(this).data('dependant');
            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('admin-users-roles') }}",
                method: "POST",
                data: {
                    select: select,
                    value: value,
                    _token: _token,
                    dependant: dependant
                },
            })
        }
    });

    $("#add_access_level").change(function() {
        if ($(this).val() == "") {
            $('#rolenameDiv').hide();
            $('#adddonorDiv').hide();
            $('#add_facility_div').hide();
            $('#add_county_div').hide();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').hide();
            $('#add_facility_div').hide();
            $('#add_clinic_div').hide();

            $('#add_clinic_div').hide();
            $('#add_bio_div').hide();
            $('#add_app_div').hide();
            $('#add_daily_div').hide();
            $('#add_weekly_div').hide();
            $('#add_mothly_div').hide();
            $('#add_month3_div').hide();
            $('#add_month6_div').hide();
            $('#add_yearly_div').hide();
            $('#add_status_div').hide();
        } else if ($(this).val() == "Admin") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').hide();
            $('#add_facility_div').hide();
            $('#add_county_div').hide();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').hide();
            $('#add_facility_div').hide();

            $('#add_clinic_div').hide();
            $('#add_bio_div').show();
            $('#add_app_div').show();
            $('#add_daily_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        } else if ($(this).val() == "Partner") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').hide();
            $('#add_facility_div').hide();
            $('#add_county_div').hide();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').show();
            $('#add_facility_div').hide();


            $('#add_clinic_div').hide();
            $('#add_bio_div').show();
            $('#add_app_div').show();
            $('#add_daily_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        } else if ($(this).val() == "Facility") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').hide();
            $('#add_facility_div').show();
            $('#add_county_div').hide();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').show();
            $('#add_clinic_div').show();

            $('#add_bio_div').show();
            $('#add_daily_div').show();
            $('#add_app_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        } else if ($(this).val() == "Donor") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').show();
            $('#add_facility_div').hide();
            $('#add_county_div').hide();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').hide();
            $('#add_clinic_div').hide();

            $('#add_bio_div').show();
            $('#add_app_div').show();
            $('#add_daily_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        } else if ($(this).val() == "County") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').hide();
            $('#add_facility_div').hide();
            $('#add_county_div').show();
            $('#add_subcounty_div').hide();
            $('#add_partner_div').hide();
            $('#add_clinic_div').hide();

            $('#add_bio_div').show();
            $('#add_app_div').show();
            $('#add_daily_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        } else if ($(this).val() == "Sub County") {
            $('#rolenameDiv').show();
            $('#adddonorDiv').hide();
            $('#add_facility_div').hide();
            $('#add_county_div').show();
            $('#add_subcounty_div').show();
            $('#add_partner_div').hide();
            $('#add_clinic_div').hide();

            $('#add_bio_div').show();
            $('#add_app_div').show();
            $('#add_daily_div').show();
            $('#add_weekly_div').show();
            $('#add_mothly_div').show();
            $('#add_month3_div').show();
            $('#add_month6_div').show();
            $('#add_yearly_div').show();
            $('#add_status_div').show();
        }
    });
    $("#add_access_level").trigger("change");
</script>


@endsection
