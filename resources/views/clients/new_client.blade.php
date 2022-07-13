@extends('layouts.master')
@section('before-css')


@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">New Client</a></li>
        <li></li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Add Client</div>
                <form role="form" method="post" action="{{route('add_client')}}">
                    {{ csrf_field() }}
                    <div class="row">
                        <input type="hidden" name="id" id="id">
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">CCC Number</label>
                            <input type="text" class="form-control" id="clinic_number" name="clinic_number" minlength="10" maxlength="10" placeholder="CCC Number">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                        </div>

                        <div class='col-sm-6'>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="col-md-4">
                                        <label for="firstName1">Date of Birth</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="date" required="" id="birth" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="birth">
                                    </div>
                                    <div class="input-group-append">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Gender</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="gender" name="gender">
                                <option value="">Please select </option>
                                @if (count($gender) > 0)
                                @foreach($gender as $gender)
                                <option value="{{$gender->id }}">{{ ucwords($gender->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Marital Status</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="marital" name="marital">
                                <option value="">Please select </option>
                                @if (count($marital) > 0)
                                @foreach($marital as $maritals)
                                <option value="{{$maritals->id }}">{{ ucwords($maritals->marital) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Treatment</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="treatment" name="treatment">
                                <option value="">Please select </option>
                                @if (count($treatment) > 0)
                                @foreach($treatment as $treatments)
                                <option value="{{$treatments->id }}">{{ ucwords($treatments->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="col-md-4">
                                        <label for="firstName1">HIV Enrollment Date</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="date" required="" id="enrollment_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="enrollment_date" max="{{date("Y-m-d")}}>
                                    </div>
                                    <div class="input-group-append">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="col-md-4">
                                        <label for="firstName1">ART Start Date</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="date" required="" id="art_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="art_date">
                                    </div>
                                    <div class="input-group-append">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Phone Number</label>
                            <input type="text" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" minlength="10" maxlength="10" placeholder="Phone No should be 10 Digits " id="phone" class="input-rounded input-sm form-control phone_no" />
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Language</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="language" name="language">
                                <option value="">Please select </option>
                                @if (count($language) > 0)
                                @foreach($language as $languages)
                                <option value="{{$languages->id }}">{{ ucwords($languages->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Enable Message Alerts?</label>
                            <select class="form-control status" id="smsenable" name="smsenable">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Receive Weekly Motivational Messages?</label>
                            <select class="form-control status" id="motivational_enable" name="motivational_enable">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Prefered Messaging Time</label>
                            <input class="form-control" required="" type="text" id="txt_time" name="txt_time" placeholder="HH:MM" />
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Client Status</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="status" name="status">
                                <option value="">Please select </option>
                                <option value="Active">Active</option>
                                <option value="Disabled">Disabled</option>
                                <option value="Transfered Out">Transfered Out</option>
                                <option value="Deceased">Deceased</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Grouping</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="group" name="group">
                                <option value="">Please select </option>
                                @if (count($grouping) > 0)
                                @foreach($grouping as $groupings)
                                <option value="{{$groupings->id }}">{{ ucwords($groupings->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Clinic</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="clinic" name="clinic">
                                <option value="">Please select </option>
                                @if (count($clinics) > 0)
                                @foreach($clinics as $clinic)
                                <option value="{{$clinic->id }}">{{ ucwords($clinic->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <!-- <div class="col-md-6 form-group mb-3">
                            <div class="form-group">
                                <label for="firstName1">County</label>
                                <select class="form-control select2" id="counties" name[]="county">
                                    <option value="">County</option>
                                    @foreach ($county as $county => $value)
                                    <option value="{{ $county }}"> {{ $value }}</option>
                                    @endforeach

                                    <option></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <div class="form-group">
                                <label for="firstName1">Sub County</label>
                                <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                                <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name[]="subcounty">
                                    <option value=""> Sub County : </option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <div class="form-group">
                                <label for="firstName1">Ward</label>
                                <span class="filter_ward_wait" style="display: none;">Loading , Please Wait ...</span>
                                <select class="form-control ward input-rounded input-sm select2" id="wards" name[]="ward">
                                    <option value=""> Ward : </option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div> -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">County</label>
                            <input type="text" class="form-control" id="county" name="county" placeholder="County">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Sub County</label>
                            <input type="text" class="form-control" id="subcounty" name="subcounty" placeholder="Sub County">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Ward</label>
                            <input type="text" class="form-control" id="ward" name="ward" placeholder="ward">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Location">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Village</label>
                            <input type="text" class="form-control" id="village" name="village" placeholder="Village">
                        </div>

                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@section('page-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>


@endsection

@section('bottom-js')
<script type="text/javascript">
    $('.counties').select2();
    $('.subcounties').select2();
    $('.wards').select2();
    $(function() {

        $("#txt_time").datetimepicker({
            format: 'HH:mm'
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
                        $('select[name="subcounty"]').append('<option value="">Please Select Sub County</option>');
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
                    url: '/get_wards/' + subcountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                        $('select[name="ward"]').empty();
                        $('select[name="ward"]').append('<option value="">Please Select Ward</option>');
                        $.each(data, function(key, value) {
                            $('select[name="ward"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="ward"]').empty();
            }
        });
    });
</script>

@endsection