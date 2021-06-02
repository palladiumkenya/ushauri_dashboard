@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                         <h4 class="card-title mb-3">A list of Partners in the system</h4>
                            <div class="col-md-12" style="margin-bottom:20px;">
                            <a type="button" href="" class="btn btn-primary btn-md pull-right">Add Donor</a>

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
                                                        <td>  {{$partner->partner_name}}</td>
                                                        <td>  {{$partner->partner_type}}</td>
                                                        <td>  {{$partner->description}}</td>
                                                        <td>  {{$partner->phone_no}}</td>
                                                        <td>  {{$partner->e_mail}}</td>
                                                        <td>  {{$partner->location}}</td>
                                                        <td>  {{$partner->status}}</td>
                                                        <td>  {{$partner->created_at}}</td>
                                                        <td>  {{$partner->updated_at}}</td>

                                                        <td>
                                                          <button onclick="" data-toggle="modal" data-target="#editDonor"
                                                          type="button" class="btn btn-primary btn-sm">Edit</button>
                                                          <button onclick="" type="button"
                                                          class="btn btn-danger btn-sm">Delete</button>


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
        "responsive":true,
        "ordering": true,
        "info": true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });</script>


@endsection
