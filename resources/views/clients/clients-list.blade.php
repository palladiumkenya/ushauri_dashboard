@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">All Clients</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <! <h4 class="card-title mb-3">Clients List</h4>
                <p>{{count($all_clients)}} List of Clients</p>
                <div class="col-md-12" style="margin-top:10px; ">

                </div>
                <div class="table-responsive">
                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Clinic Number</th>
                                <th>Serial Number</th>
                                <th>Client Name</th>
                                <th>Phone No</th>
                                <th>DOB</th>
                                <th>Clinic</th>
                                <th>Type</th>
                                <th>Treatment</th>
                                <th>Status</th>
                                <th>Enrolment Date</th>
                                <th>ART Date</th>
                                <th>Date Added</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_clients) > 0)
                            @foreach($all_clients as $result)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{$result->clinic_number}}</td>
                                <td> {{$result->file_no}}</td>
                                <td> {{$result->client_name}}</td>
                                <td> {{$result->phone_no}}</td>
                                <td> {{$result->dob}}</td>
                                <td> {{$result->name}}</td>
                                <td> {{$result->group_name}}</td>
                                <td> {{$result->client_status}}</td>
                                <td> {{$result->status}}</td>
                                <td> {{$result->enrollment_date}}</td>
                                <td> {{$result->art_date}}</td>
                                <td> {{$result->created_at}}</td>

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
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'excel',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'pdf',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'print',
                title: 'Clients List',
                filename: 'Clients List'
            }
        ]
    });
</script>


@endsection