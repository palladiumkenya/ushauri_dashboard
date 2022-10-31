@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">All HEIS</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">HEIs Listing</h4>
            <h4 class="card-title mb-3">Showing {{count($all_hei)}}</h4>
            <div class="col-md-12" style="margin-top:10px; ">
                {{ $all_hei->onEachSide(5)->links() }}
            </div>
            <div class="table-responsive">
                <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>HEI Number</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Mother CCC Number</th>
                            <th>Birth Date</th>
                            <th>Outcome</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($all_hei) > 0)
                        @foreach($all_hei as $result)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$result->hei_no}}</td>
                            <td> {{$result->f_name}}</td>
                            <td> {{$result->m_name}}</td>
                            <td> {{$result->l_name}}</td>
                            <td> {{$result->gender}}</td>
                            <td> {{ substr($result->clinic_number, 0, strpos($result->clinic_number, "-")) }}</td>
                            <td> {{$result->dob}}</td>
                            @if($result->status == 'Disabled')
                            <td> Discharged </td>
                            @else
                            <td>{{$result->status}}</td>
                            @endif
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
                title: 'HEIs List',
                filename: 'HEIs List'
            },
            {
                extend: 'csv',
                title: 'HEIs List',
                filename: 'HEIs List'
            },
            {
                extend: 'excel',
                title: 'HEIs List',
                filename: 'HEIs List'
            },
            {
                extend: 'pdf',
                title: 'HEIs List',
                filename: 'HEIs List'
            },
            {
                extend: 'print',
                title: 'HEIs List',
                filename: 'HEIs List'
            }
        ]
    });
</script>


@endsection