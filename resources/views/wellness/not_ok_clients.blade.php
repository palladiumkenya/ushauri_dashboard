@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Not Ok Wellness Response</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <! <h4 class="card-title mb-3">Check-ins List</h4>
                           <p>List of Clients Not Ok Check-Ins</p>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                @if (env('INSTANCE') === 'UshauriDOD')
                                                <th>KDOD Number</th>
                                                @else
                                                <th>Clinic Number</th>
                                                @endif
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Last Name</th>
                                                <th>Phone No</th>
                                                <th>Grouping</th>
                                                <th>Treatment</th>
                                                <th>Response</th>
                                                <th>Sent Date</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($all_not_ok_clients) > 0)
                                                @foreach($all_not_ok_clients as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->f_name}}</td>
                                                        <td>  {{$result->m_name}}</td>
                                                        <td>  {{$result->l_name}}</td>
                                                        <td>  {{$result->phone_no}}</td>
                                                        <td>  {{$result->name}}</td>
                                                        <td>  {{$result->client_status}}</td>
                                                        <td>  {{$result->msg}}</td>
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
            title: 'List of Clients Not Ok Check-Ins',
            filename: 'List of Clients Not Ok Check-Ins'
            },
            {
            extend: 'csv',
            title: 'List of Clients Not Ok Check-Ins',
            filename: 'List of Clients Not Ok Check-Ins'
            },
            {
            extend: 'excel',
            title: 'List of Clients Not Ok Check-Ins',
            filename: 'List of Clients Not Ok Check-Ins'
            },
            {
            extend: 'pdf',
            title: 'List of Clients Not Ok Check-Ins',
            filename: 'List of Clients Not Ok Check-Ins'
            },
            {
            extend: 'print',
            title: 'List of Clients Not Ok Check-Ins',
            filename: 'List of Clients Not Ok Check-Ins'
            }
        ]
    });</script>


@endsection
