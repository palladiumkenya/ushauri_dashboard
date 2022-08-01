@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection
@section('main-content')

<div class="breadcrumb">
    <ul>
        <li><a href="">HEIs Appointment Dairy</a></li>
        <li></li>
    </ul>
</div>


<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">{{count($hei_appointment)}} HEIs Appointment List</i>
            </div>
            <div class="col-md-12" style="margin-top:10px; ">
                    @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
                    {{ $hei_appointment->onEachSide(5)->links() }}
                    @endif
                </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hei_scheduled_table" class="display table table-striped table-bordered" style="width:50%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>HEI Number</th>
                                <th>HEI Full Name</th>
                                <th>Gender</th>
                                <th>HEI DOB</th>
                                <th>Mother CC Number</th>
                                <th>CareGiver Name</th>
                                <th>Appointment Date</th>
                                <th>Appoitment Type</th>
                                <th>Appoitment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($hei_appointment) > 0)
                            @foreach($hei_appointment as $result)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{$result->hei_no}}</td>
                                <td> {{$result->hei_name}}</td>
                                <td> {{$result->gender}}</td>
                                <td> {{$result->hei_dob}}</td>
                                <td> {{$result->clinic_number}}</td>
                                <td> {{$result->caregiver_name}}</td>
                                <td> {{$result->app_date}}</td>
                                <td> {{$result->app_type}}</td>
                                <td> {{$result->app_status}}</td>

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






@endsection

@section('page-js')


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js"> </script>
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

<script type="text/javascript">
    $('#hei_scheduled_table').DataTable({
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
                title: 'HEIs Appointment List',
                filename: 'HEIs Appointment List'
            },
            {
                extend: 'csv',
                title: 'HEIs Appointment List',
                filename: 'HEIs Appointment List'
            },
            {
                extend: 'excel',
                title: 'HEIs Appointment List',
                filename: 'HEIs Appointment List'
            },
            {
                extend: 'pdf',
                title: 'HEIs Appointment List',
                filename: 'HEIs Appointment List'
            },
            {
                extend: 'print',
                title: 'HEIs Appointment List',
                filename: 'HEIs Appointment List'
            }
        ]
    });

</script>

@endsection