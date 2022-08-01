@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="report/appointment_calendar">Appointment Calendar</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12 mb-4">
    <div class="card text-left">
        <div class="card-body">
            <h4 class="card-title mb-3">Appointments List</h4>

            <div class="table-responsive">
                    <table id="appointments_list" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>UPN</th>
                                    <th>File No </th>
                                    <th>Client Name</th>
                                    <th>Phone No</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Type</th>
                                    <th>Clinic</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                    @if (count($result) > 0)
                                        @foreach($result as $row)
                                            <tr>
                                                <td> {{ $loop->iteration }}</td>
                                                <td>  {{$row->clinic_number}}</td>
                                                <td>  {{$row->file_no}}</td>
                                                <td>  {{$row->f_name.' '.$row->m_name.' '.$row->l_name}}</td>
                                                <td>  {{$row->phone_no}}</td>
                                                <td>  {{$row->appntmnt_date}}</td>
                                                <td>  {{$row->appointment_types}}</td>
                                                <td>  {{$row->clinic}}</td>
                                                <td>
                                                    <input type="hidden" id="client_id" name="client_id" value="<?php echo $row->client_id; ?>" />
                                                            <input type="hidden" id="app_type_1" name="app_type_1" value="<?php echo $row->app_type_1; ?>" />
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

@endsection
