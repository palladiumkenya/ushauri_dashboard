@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">Showing {{count($clients)}}</h4>
            <div class="col-md-12" style="margin-top:10px; ">
                {{ $clients->onEachSide(5)->links() }}
            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>CCC Number</th>
                            <th>UPI Number</th>
                            <th>Client Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Phone No</th>
                            <th>Consent</th>
                            <th>Date Created</th>
                            <th>Facility Name</th>
                            <th>MFL Code</th>
                            <th>Partner Name</th>
                            <th>County</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($clients) > 0)
                        @foreach($clients as $client)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$client->clinic_number}}</td>
                            <td> {{$client->upi_no}}</td>
                            <td> {{$client->f_name}} {{$client->m_name}} {{$client->l_name}}</td>
                            <td> {{$client->gender_name}}</td>
                            <td> {{$client->dob}}</td>
                            <td> {{$client->phone_no}}</td>
                            <td> {{$client->smsenable}}</td>
                            <td> {{date('d-m-Y', strtotime($client->created_at))}}</td>
                            <td> {{$client->facility}}</td>
                            <td> {{$client->code}}</td>
                            <td> {{$client->partner}}</td>
                            <td> {{$client->county}}</td>

                            @if (Auth::user()->access_level == 'Facility')
                            <td>
                                <button onclick="editclient({{$client}});" data-toggle="modal" data-target="#editclient" type="button" class="btn btn-primary btn-sm">Edit</button>

                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @endif
                    </tbody>

                </table>

            </div>

        </div>
    </div>
</div>
<div id="editclient" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit Client Information</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('edit_client')}}">
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
                                        @error('firstname')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Middle Name</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                                        @error('last_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class='col-sm-6'>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <label for="firstName1">Date of Birth</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="date" required="" id="birth" class="form-control" data-width="100%" name="birth" max="{{date("Y-m-d")}}>
                                        @error('birth')
                                <div class=" alert alert-danger">{{ $message }}
                                                </div>
                                                @enderror

                                            </div>
                                            <div class=" input-group-append">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="gender">Gender</label>
                                    <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="gender" name="gender">
                                        <option value="">Please select </option>

                                        @if (count($gender) > 0)
                                        @foreach($gender as $genders)
                                        <option value="{{$genders->id }}" {{ $genders->id == old('gender',$client->gender) ? 'selected' : ''}}>{{ ucwords($genders->name) }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('gender')
                                    <div class=" alert alert-danger">{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="marital">Marital Status</label>
                                    <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="marital" name="marital">
                                        <option value="">Please select </option>

                                        @if (count($marital) > 0)
                                        @foreach($marital as $maritals)
                                        <option value="{{$maritals->id }}" {{ $maritals->id == old('marital',$client->marital) ? 'selected' : ''}}>{{ ucwords($maritals->marital) }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('marital')
                                    <div class=" alert alert-danger">{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="add_partner_type">Treatment</label>
                                    <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="treatment" name="treatment">
                                        <option value="">Please select </option>
                                        @if (count($treatment) > 0)
                                        @foreach($treatment as $treatments)
                                        <option value="{{$treatments->id }}" {{ $treatments->id == old('treatment',$client->treatment) ? 'selected' : ''}}>{{ ucwords($treatments->name) }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('treatment')
                                    <div class=" alert alert-danger">{{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class='col-sm-6'>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="col-md-4">
                                                <label for="firstName1">HIV Enrollment Date</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="date" required="" id="enrollment_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="enrollment_date" max="{{date("Y-m-d")}}>
                                    @error('enrollment_date')
                                    <div class=" alert alert-danger">{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class=" input-group-append">

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
                                        @error('art_date')
                                        <div class=" alert alert-danger">{{ $message }}
                                        </div>
                                        @enderror
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
                                <option value="{{$languages->id }}" {{ $languages->id == old('language',$client->language) ? 'selected' : ''}}>{{ ucwords($languages->name) }}</option>
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
                            <label for="txt_time">Prefered Messaging Time</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="txt_time" name="txt_time">
                                <option value="">Please select </option>

                                @if (count($time) > 0)
                                @foreach($time as $times)
                                <option value="{{$times->id }}" {{ $times->id == old('txt_time',$client->txt_time) ? 'selected' : ''}}>{{ ucwords($times->name) }}</option>
                                @endforeach
                                @endif
                            </select>
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
                            @error('status')
                            <div class=" alert alert-danger">{{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="group">Grouping</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="group" name="group">
                                <option value="">Please select </option>

                                @if (count($grouping) > 0)
                                @foreach($grouping as $groupings)
                                <option value="{{$groupings->id }}" {{ $groupings->id == old('group',$client->group_id) ? 'selected' : ''}}>{{ ucwords($groupings->name) }}</option>

                                @endforeach
                                @endif
                            </select>
                            @error('group')
                            <div class=" alert alert-danger">{{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Clinic</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="clinic" name="clinic">
                                <option value="">Please select </option>
                                @if (count($clinics) > 0)
                                @foreach($clinics as $clinic)
                                <option value="{{$clinic->id }}" {{ $clinic->id == old('clinicname',$client->clinic_id) ? 'selected' : ''}}>{{ ucwords($clinic->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('clinic')
                            <div class=" alert alert-danger">{{ $message }}
                            </div>
                            @enderror
                        </div>

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
                        <button type="submit" class="btn btn-block btn-primary">Update</button>

                    </div>
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
<!-- end of col -->

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    function editclient(client) {

        $('#first_name').val(client.f_name);
        $('#middle_name').val(client.m_name);
        $('#last_name').val(client.l_name);
        $('#clinic_number').val(client.clinic_number);
        $('#birth').val(client.dob);
        $('#gender').val(client.gender);
        $('#marital').val(client.marital);
        $('#treatment').val(client.client_treatment);
        $('#enrollment_date').val(client.enrollment_date);
        $('#art_date').val(client.art_date);
        $('#phone').val(client.phone_no);
        $('#language').val(client.language_id);
        $('#smsenable').val(client.smsenable);
        $('#motivational_enable').val(client.motivational_enable);
        $('#txt_time').val(client.txt_time);
        $('#status').val(client.status);
        $('#group').val(client.group_id);
        $('#clinic').val(client.clinic_id);
        $('#county').val(client.locator_county);
        $('#subcounty').val(client.locator_sub_county);
        $('#ward').val(client.locator_ward);
        $('#location').val(client.locator_location);
        $('#village').val(client.locator_village);
        $('#id').val(client.id);


    }
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
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Clients List',
                filename: 'Clients List'
            }, 'excel', 'pdf', 'print'
        ]
    });
</script>


@endsection