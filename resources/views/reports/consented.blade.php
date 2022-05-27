@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Consented Clients</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <h4 class="card-title mb-3"> Client Consent List</h4>
                        <h4 class="card-title mb-3">Showing {{count($consented_clients)}}</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                                {{ $consented_clients->onEachSide(5)->links() }}
                            </div>
                                <div class="table-responsive">
                                    <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>UPN</th>
                                                <th>Serial No</th>
                                                <th>Client Name</th>
                                                <th>Phone No</th>
                                                <th>DOB</th>
                                                <th>Type</th>
                                                <th>Consented</th>
                                                <th>Date Consented</th>
                                                <th>Status</th>
                                                <th>Treatment</th>
                                                <th>Enrollment Date</th>
                                                <th>ART Date</th>
                                                <th>Date Added</th>
                                                <th>Date Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($consented_clients) > 0)
                                                @foreach($consented_clients as $consent)
                                                    <tr>
                                                        <td> {{ $loop->iteration }}</td>
                                                        <td>  {{$consent->clinic_number}}</td>
                                                        <td>  {{$consent->file_no}}</td>
                                                        <td>  {{$consent->f_name}} {{$consent->m_name}} {{$consent->l_name}}</td>
                                                        <td>  {{$consent->phone_no}}</td>
                                                        <td>  {{$consent->dob}}</td>
                                                        <td>  {{$consent->name}}</td>
                                                        <td>  {{$consent->smsenable}}</td>
                                                        <td>  {{$consent->consent_date}}</td>
                                                        <td>  {{$consent->status}}</td>
                                                        <td>  {{$consent->client_status}}</td>
                                                        <td>  {{$consent->enrollment_date}}</td>
                                                        <td>  {{$consent->art_date}}</td>
                                                        <td>  {{$consent->created_at}}</td>
                                                        <td>  {{$consent->updated_at}}</td>
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
            title: 'Client Consent List',
            filename: 'Client Consent List'
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
    });</script>


@endsection
