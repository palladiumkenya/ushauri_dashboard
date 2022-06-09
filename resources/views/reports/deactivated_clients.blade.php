@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Deactivated Clients</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">{{count($all_deactivated_clients)}} Deactivated clients</h4>
                        <h4 class="card-title mb-3">Showing {{count($all_deactivated_clients)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $all_deactivated_clients->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>UPN</th>
                                                <th>Serial No</th>
                                                <th>Client Name</th>
                                                <th>Phone No</th>
                                                <th>DOB</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Date Added</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_deactivated_clients) > 0)
                                                @foreach($all_deactivated_clients as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->file_no}}</td>
                                                        <td>  {{$result->full_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->dob}}</td>
                                                        <td>  {{$result->name}}</td>
                                                        <td>  {{$result->client_type}}</td>
                                                        <td>  {{$result->created_at}}</td>
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
            {
            extend: 'copy',
            title: 'Deactivated clients',
            filename: 'Deactivated clients'
            },
            {
            extend: 'csv',
            title: 'Deactivated clients',
            filename: 'Deactivated clients'
            },
            {
            extend: 'excel',
            title: 'Deactivated clients',
            filename: 'Deactivated clients'
            },
            {
            extend: 'pdf',
            title: 'Deactivated clients',
            filename: 'Deactivated clients'
            },
            {
            extend: 'print',
            title: 'Deactivated clients',
            filename: 'Deactivated clients'
            }
        ]
    });</script>


@endsection
