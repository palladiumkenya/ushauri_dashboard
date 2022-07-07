@extends('layouts.master')
@section('before-css')


@endsection

@section('main-content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Add User</div>
                <form role="form" method="post" action="{{route('adduser')}}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">First name</label>
                            <input type="text" required="" class="form-control" id="firstName1" name="fname" placeholder="Enter your first name">
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="middleName1">Middle name</label>
                            <input type="text" required="" class="form-control" id="middleName1" name="mname" placeholder="Enter your middle name">
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="lastName1">Last name</label>
                            <input type="text" class="form-control" id="lastName1" name="lname" placeholder="Enter your last name">
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" required="" name="email" class=" input-rounded input-sm form-control e_mail" placeholder="Enter your Email" />
                            <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="phone">Phone No</label>
                            <input type="text" required="" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone No should be 10 Digits " id="phone_no" class="input-rounded input-sm form-control phone_no" />
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="add_access_level">Acess Level</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="add_access_level" name="add_access_level">
                                <option value="">Please select </option>
                                @if (count($access_level) > 0)
                                @foreach($access_level as $level)
                                <option value="{{$level->name }}">{{ ucwords($level->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="rolenameDiv">
                            <label for="rolename">Role Name</label>
                            <select class="form-control" data-width="100%" id="rolename" name="rolename">
                                <option value="">Please select </option>
                                <option></option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="adddonorDiv">
                            <label for="donor">Donor</label>
                            <select class="form-control" data-width="100%" id="donor" name="donor">
                                <option value="">Please select </option>
                                @if (count($donors) > 0)
                                @foreach($donors as $donor)
                                <option value="{{$donor->id }}">{{ ucwords($donor->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_county_div">
                            <label for="lastName1">County</label>
                            <select class="form-control" data-width="100%" id="county" name="county">
                                <option value="">Please select </option>
                                @if (count($counties) > 0)
                                @foreach($counties as $county)
                                <option value="{{$county->id }}">{{ ucwords($county->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_subcounty_div">
                            <label for="lastName1">Sub County</label>
                            <select class="form-control" data-width="100%" id="sub_county" name="sub_county">
                                <option value="">Please select </option>
                                <option></option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_partner_div">
                            <label for="partner">Partner</label>
                            <select class="form-control" data-width="100%" id="partner" name="partner">
                                <option value="">Please select </option>
                                @if (count($partners) > 0)
                                @foreach($partners as $partner)
                                <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_facility_div">
                            <label for="facilityname">Facility</label>
                            <select class="form-control" data-width="100%" id="facilityname" name="facilityname">
                                <option value="">Please select </option>
                                @if (count($facilities) > 0)
                                @foreach($facilities as $facility)
                                <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_clinic_div">
                            <label for="clinicname">Clinic</label>
                            <select class="form-control" data-width="100%" id="clinicname" name="clinicname">
                                <option value="">Please select </option>
                                @if (count($clinics) > 0)
                                @foreach($clinics as $clinic)
                                <option value="{{$clinic->id }}">{{ ucwords($clinic->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_bio_div">
                            <label for="bio_data">View Patients Bio Data ? : </label>
                            <select name="bio_data" class="form-control bio_data" id="bio_data" name="bio_data">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_app_div">
                            <label for="app_receive">Receive todays appointments ? : </label>
                            <select class="form-control bio_data" id="app_receive" name="app_receive">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>


                        <div class="col-md-3 form-group mb-3" id="add_daily_div">
                            <label for="daily_report">Receive Daily Reports ? : </label>
                            <select name="daily_report" class="form-control daily_report" id="daily_report" name="daily_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>



                        <div class="col-md-3 form-group mb-3" id="add_weekly_div">
                            <label>Receive Weekly Reports : </label>
                            <select name="weekly_report" class="form-control weekly_report" id="weekly_report" name="weekly_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_mothly_div">
                            <label>Receive Monthly Reports : </label>
                            <select name="monthly_report" class="form-control monthly_report" id="monthly_report" name="monthly_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>



                        <div class="col-md-3 form-group mb-3" id="add_month3_div">
                            <label>Receive 3 Months Reports : </label>
                            <select name="month3_report" class="form-control month3_report" id="month3_report" name="month3_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>



                        <div class="col-md-3 form-group mb-3" id="add_month6_div">
                            <label>Receive 6 Months Reports : </label>
                            <select name="month6_report" class="form-control month6_report" id="month6_report" name="month6_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group mb-3" id="add_yearly_div">
                            <label>Receive Yearly Reports : </label>
                            <select name="yearly_report" class="form-control yearly_report" id="yearly_report" name="yearly_report">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>


                        <div class="col-md-3 form-group mb-3" id="add_status_div">
                            <label>Status : </label>
                            <select name="status" class="form-control status" id="status" name="status">
                                <option value="">Please select</option>
                                <option value="Active">Active</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

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