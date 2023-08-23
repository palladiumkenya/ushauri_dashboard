@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
    <ul>
        <li><a href="">Edit HEI Information</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <!-- <h4 class="card-title mb-3">Clients List</h4> -->

            <div class="col-md-12" style="margin-top:10px; ">

            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>HEI Number</th>
                            <th>HEI Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($hei_list) > 0)
                        @foreach($hei_list as $result)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$result->hei_no}}</td>
                            <td> {{ucwords($result->hei_first_name)}} {{ucwords($result->hei_middle_name)}} {{ucwords($result->hei_last_name)}}</td>
                            <td> {{$result->hei_dob}}</td>
                            <td> {{$result->gender}}</td>
                            <td>
                                <button onclick="editclient({{$result}});" data-toggle="modal" data-target="#editclient" type="button" class="btn btn-primary btn-sm">Edit</button>

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

<div id="editclient" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit HEI Information</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('edit-hei')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <input type="hidden" name="client_id" id="client_id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">HEI Number</label>

                                        <input type="text" class="form-control" id="clinic_number" name="clinic_number" placeholder="HEI Number">

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
                                        <option value="{{$genders->id }}" {{ $genders->id == old('gender',$result->gender) ? 'selected' : ''}}>{{ ucwords($genders->name) }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('gender')
                                    <div class=" alert alert-danger">{{ $message }}
                                    </div>
                                    @enderror
                                </div>
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

<!-- end of col -->

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
    function editclient(result) {

        $('#first_name').val(result.hei_first_name);
        $('#middle_name').val(result.hei_middle_name);
        $('#last_name').val(result.hei_last_name);
        $('#clinic_number').val(result.hei_no);
        $('#birth').val(result.hei_dob);
        $('#gender').val(result.gender_id);
        $('#id').val(result.id);
        $('#client_id').val(result.client_id);
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
                title: 'HEI List',
                filename: 'HEI List'
            },
            {
                extend: 'csv',
                title: 'HEI List',
                filename: 'HEI List'
            },
            {
                extend: 'excel',
                title: 'HEI List',
                filename: 'HEI List'
            },
            {
                extend: 'pdf',
                title: 'HEI List',
                filename: 'HEI List'
            },
            {
                extend: 'print',
                title: 'HEI List',
                filename: 'HEI List'
            }
        ]
    });
</script>


@endsection