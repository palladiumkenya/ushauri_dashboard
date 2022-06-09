@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Tracing Cost</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">Showing {{count($tracing_cost)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $tracing_cost->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Clinic Number</th>
                                                <th>Appointment Status</th>
                                                <th>Tracer Name</th>
                                                <th>Tracing Cost</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($tracing_cost) > 0)
                                                @foreach($tracing_cost as $result)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$result->clinic_number}}</td>
                                                        <td>  {{$result->app_status}}</td>
                                                        <td>  {{$result->tracer_name}}</td>
                                                        <td>  {{$result->tracing_cost}}</td>
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
            title: 'Tracing Cost',
            filename: 'Tracing Cost'
            },
            {
            extend: 'csv',
            title: 'Tracing Cost',
            filename: 'Tracing Cost'
            },
            {
            extend: 'excel',
            title: 'Tracing Cost',
            filename: 'Tracing Cost'
            },
            {
            extend: 'pdf',
            title: 'Tracing Cost',
            filename: 'Tracing Cost'
            },
            {
            extend: 'print',
            title: 'Tracing Cost',
            filename: 'Tracing Cost'
            }
        ]
    });</script>


@endsection
