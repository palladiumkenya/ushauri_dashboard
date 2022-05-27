@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">{{count($active_facilities)}} Active Facilities List</h4>
                            <div class="col-md-12" style="margin-top:10px; ">

                            </div>

                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>MFL CODE</th>
                                                <th>FACILITY</th>
                                                <th>PARTNER</th>
                                                <th>COUNTY</th>
                                                <th>SUB COUNTY</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($active_facilities) > 0)
                                                @foreach($active_facilities as $active_facility)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$active_facility->code}}</td>
                                                        <td>  {{$active_facility->facility}}</td>
                                                        <td>  {{$active_facility->partner}}</td>
                                                        <td>  {{$active_facility->county}}</td>
                                                        <td>  {{$active_facility->subcounty}}</td>


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
            title: 'Active Facilities List',
            filename: 'Active Facilities List'
            },
            {
            extend: 'csv',
            exportOptions: {
                  columns: ':visible',
                  modifier:{
                    page:'all'
                  }
              },
            title: 'Active Facilities List',
            filename: 'Active Facilities List'
            }, 'excel', 'pdf', 'print'
        ]
    });</script>


@endsection
