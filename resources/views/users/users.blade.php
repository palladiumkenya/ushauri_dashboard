@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">System Users</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
        <h4 class="card-title mb-3">{{count($all_users)}} Users</h4>

            <div style="margin-bottom:10px; ">
                <a type="button" href="{{route('admin-users-form')}}" class="btn btn-primary btn-md pull-right">Add User</a>
            </div>

            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>User Name</th>
                            <th>DOB</th>
                            <th>Phone No</th>
                            <th>Email</th>
                            <th>Access Level</th>
                            <th>Clinic Name</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Time Stamp</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($all_users) > 0)
                        @foreach($all_users as $user)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$user->user_name}}</td>
                            <td> {{$user->dob}}</td>
                            <td> {{$user->phone_no}}</td>
                            <td> {{$user->e_mail}}</td>
                            <td> {{$user->access_level}}</td>
                            <td> {{$user->clinic_name}}</td>
                            <td> {{$user->status}}</td>
                            <td> {{$user->created_at}}</td>
                            <td> {{$user->updated_at}}</td>
                            <td>
                            <button onclick="resetUser({{$user->id}});" type="button" class="btn btn-success btn-sm">Reset</button>
                                <button onclick="edituser({{$user}});" data-toggle="modal" data-target="#edituser" type="button" class="btn btn-primary btn-sm">Edit</button>

                            </td>
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

<!-- Reset Modal -->
<div class="modal fade" id="ResetModal" tabindex="-1" role="dialog" aria-labelledby="ResetTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div  class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ResetTitle">Reset User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset this user's password?.</p>

            </div>
            <form id="reset_form">
                                {{ csrf_field() }}
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="reset" type="submit" class="btn btn-primary">Reset</button>

            </div>
</form>
        </div>
    </div>
</div>
<!-- edit goes here -->
<div id="edituser" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit User</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('edituser')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">First name</label>
                                        <input type="text" required="" class="form-control" id="fname" name="fname" placeholder="Enter your first name">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="middleName1">Middle name</label>
                                        <input type="text" required="" class="form-control" id="mname" name="mname" placeholder="Enter your middle name">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="lastName1">Last name</label>
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter your last name">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="exampleInputEmail1">Email address</label>
                                        <input type="email" required="" id="email" name="email" class=" input-rounded input-sm form-control e_mail" placeholder="Enter your Email" />
                                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="phone">Phone No</label>
                                        <input type="text" required="" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone No should be 10 Digits " id="phone" class="input-rounded input-sm form-control phone_no" />
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
                                            @if (count($roles) > 0)
                                            @foreach($roles as $role)
                                            <option value="{{$role->id }}" {{ $role->id == old('rolename',$user->role_id) ? 'selected' : ''}}>{{ ucwords($role->name) }}</option>
                                            @endforeach
                                            @endif

                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group mb-3" id="adddonorDiv">
                                        <label for="donor">Donor</label>
                                        <select class="form-control" data-width="100%" id="donor" name="donor">
                                            <option value="">Please select </option>
                                            @if (count($donors) > 0)
                                            @foreach($donors as $donor)
                                            <option value="{{$donor->id }}" >{{ ucwords($donor->name) }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-3 form-group mb-3" id="add_county_div">
                                        <label for="county">County</label>
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
                                        <label for="sub_county">Sub County</label>
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
                                            <option value="{{$partner->id }}" {{ $partner->id == old('partner',$user->partner_id) ? 'selected' : ''}}>{{ ucwords($partner->name) }}</option>
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
                                            <option value="{{$facility->code }}" {{ $facility->code == old('facilityname',$user->facility_id) ? 'selected' : ''}}>{{ ucwords($facility->name) }}</option>
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
                                            <option value="{{$clinic->id }}" {{ $clinic->id == old('clinicname',$user->clinic_id) ? 'selected' : ''}}>{{ ucwords($clinic->name) }}</option>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>



<div id="DeleteModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to Delete this user's password?.</p>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                <button id="delete" type="button" class="btn btn-danger" data-person_id="">Delete</button>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">


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
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

    function edituser(user) {

        if (user.access_level == "") {
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
        } else if (user.access_level == "Admin") {
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
        } else if (user.access_level == "Partner") {
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
        } else if (user.access_level == "Facility") {
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
        } else if (user.access_level == "Donor") {
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
        } else if (user.access_level == "County") {
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
        } else if (user.access_level == "Sub County") {
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

        $('#fname').val(user.f_name);
        $('#mname').val(user.m_name);
        $('#lname').val(user.l_name);
        $('#email').val(user.e_mail);
        $('#phone').val(user.phone_no);
        $('#add_access_level').val(user.access_level);
        $('#rolename').val(user.role_id);
        $('#donor').val(user.donor_id);
        $('#county').val(user.county_id);
        $('#sub_county').val(user.subcounty_id);
        $('#partner').val(user.partner_id);
        $('#facilityname').val(user.facility_id);
        $('#clinicname').val(user.clinic_id);
        $('#bio_data').val(user.view_client);
        $('#app_receive').val(user.rcv_app_list);
        $('#daily_report').val(user.daily_report);
        $('#weekly_report').val(user.daily_report);
        $('#monthly_report').val(user.monthly_report);
        $('#month3_report').val(user.month3_report);
        $('#month6_report').val(user.month6_report);
        $('#yearly_report').val(user.yearly_report);
        $('#status').val(user.status);
        $('#id').val(user.id);
    }

    function resetuser(user) {

    }

    function deleteUser(id) {
        $('#DeleteModal').modal('show');
        console.log(id);
        $(document).off("click", "#delete").on("click", "#delete", function(event) {
            $.ajax({
                type: "POST",
                url: '/delete/user',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token()}}"
                },
                dataType: "json",
                success: function(data) {
                    toastr.success(data.details);
                    $('#DeleteModal').modal('hide');
                }
            })
        });
    }

    function resetUser(id) {
        $('#ResetModal').modal('show');
        console.log(id);
        $(document).off("click", "#reset").on("click", "#reset", function(event) {
            $.ajax({
                type: "POST",
                url: '/reset/user',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token()}}"
                },
                dataType: "json",
                success: function(data) {
                    $('#ResetModal').modal('hide');
                }


            })
        });
    };

    var loginForm = $("#reset_form");

            loginForm.submit(function (e) {
                $('#ResetModal').modal('hide');
                e.preventDefault();
                var thisForm = $(this);
                var endPoint = thisForm.attr("action") || window.location.href;
                var method = thisForm.attr("method");
                var formData = thisForm.serialize();

                console.log(endPoint);
                console.log(method);
                Swal.fire({
                    title: "User has been reset successfull",
                    showConfirmButton: true,
                    allowOutsideClick: true
                });

                this.submit();

            });


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