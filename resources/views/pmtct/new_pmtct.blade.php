@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">New PMTCT Listing</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">New PMTCT Listing</h4>
            <h4 class="card-title mb-3">Showing </h4>

            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Clinic Number</th>
                            <th>Client Name</th>

                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td> {{ $loop->iteration }}</td>
                        </tr>

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
                title: 'PMTCT List',
                filename: 'PMTCT List'
            },
            {
                extend: 'csv',
                title: 'PMTCT List',
                filename: 'PMTCT List'
            },
            {
                extend: 'excel',
                title: 'PMTCT List',
                filename: 'PMTCT List'
            },
            {
                extend: 'pdf',
                title: 'PMTCT List',
                filename: 'PMTCT List'
            },
            {
                extend: 'print',
                title: 'PMTCT List',
                filename: 'PMTCT List'
            }
        ]
    });
</script>


@endsection