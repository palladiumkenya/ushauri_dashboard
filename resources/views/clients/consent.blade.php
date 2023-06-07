@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Client Consent</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="card text-left">

        <div class="card-body">
            <h4 class="card-title mb-3">{{count($consented_clients)}} Non Consented Clients </h4>
            <div style="margin-bottom:10px; ">

            </div>
            <div class="col-md-12" style="margin-top:10px; ">

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
                            <th>Sms Time</th>
                            <th>Status</th>

                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if (count($consented_clients) > 0)
                        @foreach($consented_clients as $consent)
                        <tr>
                            <td> {{ $loop->iteration }}</td>
                            <td> {{$consent->clinic_number}}</td>
                            <td> {{$consent->file_no}}</td>
                            <td> {{$consent->full_name}}</td>
                            <td> {{$consent->phone_no}}</td>
                            <td> {{$consent->dob}}</td>
                            <td> {{$consent->name}}</td>
                            <td> {{$consent->smsenable}}</td>
                            <td> {{$consent->consent_date}}</td>
                            <td> {{$consent->txt_time}}</td>
                            <td> {{$consent->status}}</td>

                            <td>
                                <button onclick="consentclient({{$consent}});" data-toggle="modal" data-target="#consentModal" type="button" class="btn btn-primary btn-sm">Consent</button>

                            </td>
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

<div id="consentModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <div class="card-title mb-3">Consent Client</div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form role="form" method="post" action="{{route('report-clients-consent')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">CCC Number</label>
                                        <input type="text" class="form-control" id="clinic_number" name="clinic_number" placeholder="CCC Number" readonly />
                                    </div>
                                    <div class='col-sm-6'>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <label for="firstName1">Consent Date</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="date" required="" id="consent_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="consent_date">
                                                </div>
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary" type="button">
                                                        <i class="icon-regular i-Calendar-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="picker1">Smsenable</label>
                                        <select id="smsenable" name="smsenable" class="form-control" required="">
                                            <option>Select</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="picker1">Language</label>
                                        <select id="language_id" name="language_id" class="form-control" required="">
                                            <option>Select Language</option>
                                            <option value="1">Swahili</option>
                                            <option value="2">English</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="picker1">Motivational Alerts</label>
                                        <select id="motivational_enable" name="motivational_enable" class="form-control" required="">
                                            <option>Enable Weekly motivational alerts</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Prefered Time</label>
                                        <input class="form-control" required="" type="text" id="txt_time" name="txt_time" placeholder="HH:MM" />
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="firstName1">Phone Number</label>
                                        <input type="text" class="form-control" id="phone_no" name="phone_no" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone Number">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

@endsection

@section('page-js')


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

<script type="text/javascript">
    $(function() {

        $("#txt_time").datetimepicker({
            format: 'HH:mm'
        });

    });

    function consentclient(consent) {

        $('#clinic_number').val(consent.clinic_number);
        $('#phone_no').val(consent.phone_no);
       // $('#id').val(consent.id);

    }


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
        buttons: [
            {
            extend: 'copy',
            title: 'Non Consented Clients List',
            filename: 'Non Consented Clients List'
            },
            {
            extend: 'csv',
            title: 'Non Consented Clients List',
            filename: 'Non Consented Clients List'
            },
            {
            extend: 'excel',
            title: 'Non Consented Clients List',
            filename: 'Non Consented Clients List'
            },
            {
            extend: 'pdf',
            title: 'Non Consented Clients List',
            filename: 'Non Consented Clients List'
            },
            {
            extend: 'print',
            title: 'Non Consented Clients List',
            filename: 'Non Consented Clients List'
            }
        ]
    });
</script>


@endsection