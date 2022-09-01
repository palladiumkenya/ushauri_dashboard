@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">Showing {{count($appointments)}}</h4>
            <div class="col-md-12" style="margin-top:10px; ">
                {{ $appointments->onEachSide(5)->links() }}
            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>CCC Number</th>
                            <th>Client Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Phone No</th>
                            <th>Consent</th>
                            <th>Appointment Type</th>
                            <th>Appointment Date</th>
                            <th>Appointment Status</th>
                            <th>Date Created</th>
                            <th>Facility Name</th>
                            <th>MFL Code</th>
                            <th>Partner Name</th>
                            <th>County</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($appointments) > 0)
                        @foreach($appointments as $appointment)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$appointment->clinic_number}}</td>
                            <td> {{$appointment->f_name}} {{$appointment->m_name}} {{$appointment->l_name}}</td>
                            <td> {{$appointment->gender}}</td>
                            <td> {{$appointment->dob}}</td>
                            <td> {{$appointment->phone_no}}</td>
                            <td> {{$appointment->smsenable}}</td>
                            <td> {{$appointment->app_type}}</td>
                            <td> {{$appointment->appntmnt_date}}</td>
                            <td> {{$appointment->app_status}}</td>
                            <td> {{date('d-m-Y', strtotime($appointment->created_at))}}</td>
                            <td> {{$appointment->facility}}</td>
                            <td> {{$appointment->code}}</td>
                            <td> {{$appointment->partner}}</td>
                            <td> {{$appointment->county}}</td>

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