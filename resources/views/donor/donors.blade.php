@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Donor</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">{{count($all_donor)}}  Donors </h4>
            <div class="col-md-12" style="margin-bottom:20px;">
                <a type="button" href="{{route('admin-donors-form')}}" class="btn btn-primary btn-md pull-right">Add Donor</a>

            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Donor Name</th>
                            <th>Description</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Time Stamp</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($all_donor) > 0)
                        @foreach($all_donor as $donor)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$donor->name}}</td>
                            <td> {{$donor->description}}</td>
                            <td> {{$donor->phone_no}}</td>
                            <td> {{$donor->e_mail}}</td>
                            <td> {{$donor->status}}</td>
                            <td> {{$donor->created_at}}</td>
                            <td> {{$donor->updated_at}}</td>

                            <td>
                                <button onclick="editdonor({{ $donor }});" data-toggle="modal" data-target="#editdonor" type="button" class="btn btn-primary btn-sm">Edit</button>
                                <button onclick="deletedonor({{ $donor->id }});" type="button" class="btn btn-danger btn-sm">Delete</button>


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
<div id="editdonor" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit Donor</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">

                            <form role="form" method="post" action="{{route('editdonor')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Donor name">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Phone No</label>
                                        <input type="text" required="" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone No should be 10 Digits " id="phone_no" class="input-rounded input-sm form-control phone_no" />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="exampleInputEmail1">Email</label>
                                        <input type="email" required="" name="email" class=" input-rounded input-sm form-control e_mail" id="email" placeholder="Enter your Email" />

                                    </div>
                                    <div class="col-md-6 form-group mb-3" id="add_status_div">
                                        <label>Status : </label>
                                        <select name="status" class="form-control status" id="status" name="status">
                                            <option value="">Please select</option>
                                            <option value="Active">Active</option>
                                            <option value="Disabled">Disabled</option>
                                        </select>
                                    </div>
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

<div id="deletedonor" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Donor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Donor?</p>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                <button id="delete" type="button" class="btn btn-danger">Delete</button>
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
<script type="text/javascript">
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
            title: 'Donors List',
            filename: 'Donors'
            },
            {
            extend: 'csv',
            title: 'Donors List',
            filename: 'Donors'
            },
            {
            extend: 'excel',
            title: 'Donors List',
            filename: 'Donors'
            },
            {
            extend: 'pdf',
            title: 'Donors List',
            filename: 'Donors'
            },
            {
            extend: 'print',
            title: 'Donors List',
            filename: 'Donors'
            }
        ]
    });

    function editdonor(donor) {

        $('#name').val(donor.name);
        $('#description').val(donor.description);
        $('#phone_no').val(donor.phone_no);
        $('#email').val(donor.e_mail);
        $('#status').val(donor.status);
        $('#id').val(donor.id);
    }

    function deletedonor(id) {
        $('#deletedonor').modal('show');
        console.log(id);
        $(document).off("click", "#delete").on("click", "#delete", function(event) {
            $.ajax({
                type: "POST",
                url: '/delete/donor',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token()}}"
                },
                dataType: "json",
                success: function(data) {
                    toastr.success(data.details);
                    $('#deletedonor').modal('hide');
                }
            })
        });
    }
</script>


@endsection