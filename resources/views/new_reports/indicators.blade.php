@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3">Indicators Definition Table</h4>

                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>INDICATOR TERM</th>
                                                <th>DESCRIPTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($indicators) > 0)
                                                @foreach($indicators as $indicator)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$indicator->name}}</td>
                                                        <td>  {{$indicator->description}}</td>

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
            title: 'Defination List',
            filename: 'Defination List'
            },
            {
            extend: 'csv',
            exportOptions: {
                  columns: ':visible',
                  modifier:{
                    page:'all'
                  }
              },
            title: 'Defination List',
            filename: 'Defination List'
            }, 'excel', 'pdf', 'print'
        ]
    });</script>


@endsection
