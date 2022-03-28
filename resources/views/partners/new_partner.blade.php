@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">{{count($all_partners)}} Partners </h4>
            <div class="col-md-12" style="margin-bottom:20px;">
                <a type="button" href="{{route('admin-partners-form')}}" class="btn btn-primary btn-md pull-right">Add Partner</a>

            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Partnar Name</th>
                            <th>Partner Type</th>
                            <th>Description</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Time Stamp</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($all_partners) > 0)
                        @foreach($all_partners as $partner)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$partner->partner_name}}</td>
                            <td> {{$partner->partner_type}}</td>
                            <td> {{$partner->description}}</td>
                            <td> {{$partner->phone_no}}</td>
                            <td> {{$partner->e_mail}}</td>
                            <td> {{$partner->location}}</td>
                            <td> {{$partner->status}}</td>
                            <td> {{$partner->created_at}}</td>
                            <td> {{$partner->updated_at}}</td>

                            <td>
                                <button onclick="editpartner({{ $partner }});" data-toggle="modal" data-target="#editpartner" type="button" class="btn btn-primary btn-sm">Edit</button>
                                <button onclick="deletePartner({{ $partner->id }});" data-target="#deletepartner" type="button" class="btn btn-danger btn-sm">Delete</button>


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
<div id="editpartner" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Edit Partner</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">

                            <form role="form" method="post" action="{{route('editpartner')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Partner Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Partner name">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Phone No</label>
                                        <input type="text" required="" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone No should be 10 Digits " id="phone_no" class="input-rounded input-sm form-control phone_no" />
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="add_partner_type">Partner Type</label>
                                        <select class="form-control dynamic" data-width="100%" id="partner_type" name="partner_type">
                                            <option value="">Please select </option>
                                            @if (count($partner_type) > 0)
                                            @foreach($partner_type as $type)
                                            <option value="{{$type->id }}">{{ ucwords($type->name) }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" placeholder="Location">
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
<div id="deletedonor" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Delete Partner</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Partner?</p>
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
    function editpartner(partner) {

        $('#name').val(partner.partner_name);
        $('#description').val(partner.description);
        $('#phone_no').val(partner.phone_no);
        $('#email').val(partner.e_mail);
        $('#status').val(partner.status);
        $('#partner_type').val(partner.partner_type);
        $('#location').val(partner.location);
        $('#id').val(partner.id);
    }

    function deletePartner(id) {
        $('#deletedonor').modal('show');
        console.log(uid);
        $(document).off("click", "#delete").on("click", "#delete", function(event) {
            $.ajax({
                type: "POST",
                url: '/delete/partner',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token()}}"
                },
                dataType: "json",
                success: function(data) {
                    //.success(data.details);
                    $('#deletepartner').modal('hide');
                }
            })
        });
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
                title: 'List Of Partners',
                filename: 'List Of Partners'
            },
            {
                extend: 'csv',
                title: 'List Of Partners',
                filename: 'List Of Partners'
            },
            {
                extend: 'excel',
                title: 'List Of Partners',
                filename: 'List Of Partners'
            },
            {
                extend: 'pdf',
                title: 'List Of Partners',
                filename: 'List Of Partners'
            },
            {
                extend: 'print',
                title: 'List Of Partners',
                filename: 'List Of Partners'
            }
        ]
    });
</script>


@endsection