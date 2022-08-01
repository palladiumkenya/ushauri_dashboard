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
    <div class="col-lg-12 col-md-12">
        <div class="card mb-4">
            <div class="panel-heading">
                <i class="icon-table">Stable Clients</i>
            </div>
            <div class="card-body">
                <h4 class="card-title mb-3">Showing {{count($all_dsd_clients)}}</h4>
                <div class="col-md-12" style="margin-top:10px; ">
                    @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
                    {{ $all_dsd_clients->onEachSide(5)->links() }}
                    @endif
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

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_dsd_clients) > 0)
                            @foreach($all_dsd_clients as $clients)
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