@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Facilities Activation</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">{{count($admin_facilities)}} Non Activated Facilities Lists</h4>
            <div class="col-md-12" style="margin-top:10px; ">


                <div class="table-responsive">
                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Facility Name</th>
                                <th>MFL Code</th>
                                <th>County</th>
                                <th>Sub County</th>
                                <th>Consituency</th>
                                <th>Owner</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($admin_facilities) > 0)
                            @foreach($admin_facilities as $result)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{$result->facility_name}}</td>
                                <td> {{$result->code}}</td>
                                <td> {{$result->county_name}}</td>
                                <td> {{$result->sub_county_name}}</td>
                                <td> {{$result->consituency_name}}</td>
                                <td> {{$result->owner}}</td>
                                <td>
                                    <button onclick="addfacility({{$result}});" data-toggle="modal" data-target="#addfacility" type="button" class="btn btn-primary btn-sm">Add Facility</button>
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
</div>
<!-- end of col -->
<div id="addfacility" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Add Facility</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('add_facility')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <input type="hidden" name="county" id="county">
                                    <input type="hidden" name="sub_county" id="sub_county">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Facility Name</label>
                                        <input type="text" class="form-control" id="facility_name" name="facility_name" placeholder="Facility Name" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">MFL Code</label>
                                        <input type="text" class="form-control" id="mfl_code" name="mfl_code" placeholder="MFL Code" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Facility Type</label>
                                        <input type="text" class="form-control" id="facility_type" name="facility_type" placeholder="Facility Type" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Owner</label>
                                        <input type="text" class="form-control" id="owner" name="owner" placeholder="Owner" readonly />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Level</label>
                                        <input type="text" class="form-control" id="level" name="level" placeholder="Level" readonly />
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="picker1">Partner Name</label>
                                        <select id="partner" name="partner" class="form-control" required="">
                                            <option>Select Partner</option>

                                            @if (count($all_partners) > 0)
                                            @foreach($all_partners as $partner)
                                            <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                                            @endforeach
                                            @endif

                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Contact Name</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Contact Name" />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Average No of Active Clients</label>
                                        <input type="text" class="form-control" id="average_clients" name="average_clients" placeholder="Average No of Active Clients" />
                                    </div>

                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Add Facility</button>
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

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    function addfacility(result) {

        $('#facility_name').val(result.facility_name);
        $('#facility_type').val(result.facility_type);
        $('#mfl_code').val(result.code);
        $('#owner').val(result.owner);
        $('#level').val(result.level);
        $('#county').val(result.county_id);
        $('#sub_county').val(result.sub_county_id);
        $('#id').val(result.id);

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
        buttons: [
            {
            extend: 'copy',
            title: 'List Of Non Ushauri Facilities',
            filename: 'List Of Non Ushauri Facilities'
            },
            {
            extend: 'csv',
            title: 'List Of Non Ushauri Facilities',
            filename: 'List Of Non Ushauri Facilities'
            },
            {
            extend: 'excel',
            title: 'List Of Non Ushauri Facilities',
            filename: 'List Of Non Ushauri Facilities'
            },
            {
            extend: 'pdf',
            title: 'List Of Non Ushauri Facilities',
            filename: 'List Of Non Ushauri Facilities'
            },
            {
            extend: 'print',
            title: 'List Of Non Ushauri Facilities',
            filename: 'List Of Non Ushauri Facilities'
            }
        ]
    });
</script>


@endsection