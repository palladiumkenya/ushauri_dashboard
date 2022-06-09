@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection
@section('main-content')

<div class="breadcrumb">
    <ul>
        <li><a href="">DSD Diary</a></li>
        <li></li>
    </ul>
</div>


<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">Advanced Clients</i>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-3">Showing {{count($all_clients_duration_less_advanced)}}</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    {{ $all_clients_duration_less_advanced->onEachSide(5)->links() }}
                </div>
                <div class="table-responsive">
                    <table id="less_advanced_table" class="display table table-striped table-bordered" style="width:50%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>CCC Number</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Status</th>
                                <th>Appointment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_clients_duration_less_advanced) > 0)
                            @foreach($all_clients_duration_less_advanced as $clients)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{ ucwords($clients->clinic_number)}}</td>
                                <td> {{$clients->f_name}}</td>
                                <td> {{$clients->m_name}}</td>
                                <td> {{$clients->l_name}}</td>
                                <td> {{$clients->duration_less}}</td>
                                <td> {{$clients->appntmnt_date}}</td>


                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-sm-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">Well Clients</i>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-3">Showing {{count($all_clients_duration_less_well)}}</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    {{ $all_clients_duration_less_well->onEachSide(5)->links() }}
                </div>
                <div class="table-responsive">
                    <table id="less_well_table" class="display table table-striped table-bordered" style="width:50%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>CCC Number</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Status</th>
                                <th>Appointment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_clients_duration_less_well) > 0)
                            @foreach($all_clients_duration_less_well as $clients)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{ ucwords($clients->clinic_number)}}</td>
                                <td> {{$clients->f_name}}</td>
                                <td> {{$clients->m_name}}</td>
                                <td> {{$clients->l_name}}</td>
                                <td> {{$clients->duration_less}}</td>
                                <td> {{$clients->appntmnt_date}}</td>

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

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">Stable Clients</i>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-3">Showing {{count($all_clients_duration_more_stable)}}</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    {{ $all_clients_duration_more_stable->onEachSide(5)->links() }}
                </div>
                <div class="table-responsive">
                    <table id="more_stable_table" class="display table table-striped table-bordered" style="width:50%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>CCC Number</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Status</th>
                                <th>Stability Status</th>
                                <th>Facility Based</th>
                                <th>Community Based</th>
                                <th>Refill Date</th>
                                <th>Clinical Review Date</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_clients_duration_more_stable) > 0)
                            @foreach($all_clients_duration_more_stable as $clients)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{ ucwords($clients->clinic_number)}}</td>
                                <td> {{$clients->f_name}}</td>
                                <td> {{$clients->m_name}}</td>
                                <td> {{$clients->l_name}}</td>
                                <td> {{$clients->duration_more}}</td>
                                <td> {{$clients->stability_status}}</td>
                                <td> {{$clients->facility_based}}</td>
                                <td> {{$clients->community_based}}</td>
                                <td> {{$clients->refill_date}}</td>
                                <td> {{$clients->clinical_visit_date}}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">Unstable Clients</i>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-3">Showing {{count($all_clients_duration_more_unstable)}}</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    {{ $all_clients_duration_more_unstable->onEachSide(5)->links() }}
                </div>
                <div class="table-responsive">
                    <table id="more_unstable_table" class="display table table-striped table-bordered" style="width:50%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>CCC Number</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Status</th>


                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_clients_duration_more_unstable) > 0)
                            @foreach($all_clients_duration_more_unstable as $clients)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{ ucwords($clients->clinic_number)}}</td>
                                <td> {{$clients->f_name}}</td>
                                <td> {{$clients->m_name}}</td>
                                <td> {{$clients->l_name}}</td>
                                <td> {{$clients->duration_more}}</td>


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
    $('#less_advanced_table').DataTable({
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
                title: 'Advanced Clients',
                filename: 'Advanced Clients'
            },
            {
                extend: 'csv',
                title: 'Client Consent List',
                filename: 'Client Consent List'
            },
            {
                extend: 'excel',
                title: 'Client Consent List',
                filename: 'Client Consent List'
            },
            {
                extend: 'pdf',
                title: 'Client Consent List',
                filename: 'Client Consent List'
            },
            {
                extend: 'print',
                title: 'Client Consent List',
                filename: 'Client Consent List'
            }
        ]
    });
    $('#less_well_table').DataTable({
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
                title: 'Well Clients',
                filename: 'Well Clients'
            },
            {
                extend: 'csv',
                title: 'Well Clients',
                filename: 'Well Clients'
            },
            {
                extend: 'excel',
                title: 'Well Clients',
                filename: 'Well Clients'
            },
            {
                extend: 'pdf',
                title: 'Well Clients',
                filename: 'Well Clients'
            },
            {
                extend: 'print',
                title: 'Well Clients',
                filename: 'Well Clients'
            }
        ]
    });
    $('#more_unstable_table').DataTable({
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
                title: 'Unstable Clients',
                filename: 'Unstable Clients'
            },
            {
                extend: 'csv',
                title: 'Unstable Clients',
                filename: 'Unstable Clients'
            },
            {
                extend: 'excel',
                title: 'Unstable Clients',
                filename: 'Unstable Clients'
            },
            {
                extend: 'pdf',
                title: 'Unstable Clients',
                filename: 'Unstable Clients'
            },
            {
                extend: 'print',
                title: 'Unstable Clients',
                filename: 'Unstable Clients'
            }
        ]
    });
    // multi column ordering
    $('#more_stable_table').DataTable({
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
                title: 'Stable Clients',
                filename: 'Stable Clients'
            },
            {
                extend: 'csv',
                title: 'Stable Clients',
                filename: 'Stable Clients'
            },
            {
                extend: 'excel',
                title: 'Stable Clients',
                filename: 'Stable Clients'
            },
            {
                extend: 'pdf',
                title: 'Stable Clients',
                filename: 'Stable Clients'
            },
            {
                extend: 'print',
                title: 'Stable Clients',
                filename: 'Stable Clients'
            }
        ]
    });
</script>

@endsection