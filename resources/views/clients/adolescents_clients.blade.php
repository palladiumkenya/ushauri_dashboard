@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">Adolescent Clients</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <! <h4 class="card-title mb-3">ADOLESCENT Grouping List</h4>
                <div class="col-md-12" style="margin-top:10px; ">

                </div>
                <div class="table-responsive">
                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Clinic Number</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Phone No</th>
                                <th>Grouping</th>
                                <th>Treatment</th>
                                <th>Date Enrolled</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if (count($all_adolescents_clients) > 0)
                            @foreach($all_adolescents_clients as $result)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{$result->clinic_number}}</td>
                                <td> {{$result->f_name}}</td>
                                <td> {{$result->m_name}}</td>
                                <td> {{$result->l_name}}</td>
                                <td> {{$result->phone_no}}</td>
                                <td> {{$result->name}}</td>
                                <td> {{$result->client_status}}</td>
                                <td> {{$result->enrollment_date}}</td>
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
    // multi column ordering
    $(document).ready(function() {
        $('#multicolumn_ordering_table').DataTable({
            columnDefs: [{
                    targets: [0],
                    orderData: [0, 1]
                },
                {
                    targets: [1],
                    orderData: [1, 0]
                },
                {
                    targets: [2],
                    orderData: [2, 0]
                }
            ],
            "paging": true,
            "responsive": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [5, 10, 25], // Set the desired values for the number of entries per page
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy'
                },
                {
                    extend: 'csv'
                },
                {
                    extend: 'excel'
                },
                {
                    extend: 'pdf'
                },
                {
                    extend: 'print'
                }
            ]
        });
    });
</script>


@endsection