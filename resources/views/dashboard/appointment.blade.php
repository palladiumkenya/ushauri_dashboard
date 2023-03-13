@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

<style rel="stylesheet" type="text/css">
    .radial-01 {
        top: 15px;
        right: 15px;
        float: right;
        position: absolute;
        width: 40px;
        height: 40px;
        text-align: center;
        border-radius: 100%;

    }

    .radial-01 p {
        position: absolute;
        left: -25px;
        bottom: -55px;
        z-index: 50;
        width: 100px;
    }

    .radial-01 .radial-01-number {
        position: absolute;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        background-color: #fff;
        border-radius: 100%;
        padding-top: 11px;
        z-index: 20;
    }

    .radial-01 .radial-01-number {
        font-weight: 700;
        font-size: 10px;
    }

    .radial-01 .radial-01-number .radial-01-syb {
        font-weight: 700;
        font-size: 10px;
    }

    .radial-01>span.radial-01-border-r:before {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #ccc;
        border-radius: 100%;
        z-index: 5;
    }

    .radial-01>span.radial-01-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #006838;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 15px);
    }

    .radial-01>span.radial-02-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #97080F;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 30px);
    }

    .radial-01>span.radial-03-border-r:after {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #01058A;
        border-radius: 100%;
        z-index: 10;
        clip: rect(0px, 60px, 60px, 40px);
    }

    .radial-01>span.radial-01-border-l:before {
        content: " ";
        display: block;
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: #ccc;
        border-radius: 100%;
        z-index: 5;
    }

    .tab-content {
        font-family: 'Inter';
        font-style: normal;
        font-weight: 500;
        font-size: 16px;
        line-height: 16px;

    }

    .TX_Curr {

        background: #FFFFFF;
        /* box-shadow: 0px 10px 30px rgba(255, 153, 58, 0.4); */
        border-radius: 10px;

    }

    .Booked {

        background: #FFFFFF;
        /* border: 1px solid #E5F7FF; */
        border-radius: 10px;

    }

    .Consented {

        background: #FFFFFF;
        /* box-shadow: 0px 10px 30px rgba(184, 146, 222, 0.4); */
        border-radius: 10px;

    }

    .Messages {

        background: #663399;
        box-shadow: 0px 10px 30px rgba(138, 197, 62, 0.4);
        border-radius: 10px;

    }

    .Kept {

        background: #FFFFFF;
        /* box-shadow: 0px 10px 30px rgba(255, 153, 58, 0.4); */
        border-radius: 10px;

    }

    .Future {

        background: #FFFFFF;
        /* border: 1px solid #E5F7FF; */
        border-radius: 10px;

    }

    .Not_Kept {

        background: #FFFFFF;
        /* box-shadow: 0px 10px 30px rgba(184, 146, 222, 0.4); */
        border-radius: 10px;

    }

    .Indicator {
        font-style: normal;
        font-weight: 700;
        font-size: 12px;
        line-height: 140%;
        /* identical to box height, or 24px */
        letter-spacing: -0.3px;
        text-transform: uppercase;

        color: #0020C6;
    }

    .Definition {
        font-style: normal;
        font-weight: 700;
        line-height: 120%;
        /* identical to box height, or 20px */
        letter-spacing: -0.3px;
        text-transform: capitalize;
        font-size: 12px;

        color: #888888;
    }

    .Definitions {
        font-style: normal;
        font-size: 12px;
        color: #888888;
    }

    .Indications {
        box-sizing: border-box;
        background: rgba(229, 247, 255, 0.28);
        border: 1px solid #E5F7FF;
        border-radius: 10px;
        font-size: 14px;
    }

    .box_pec {
        top: 12px;
        right: 10px;
        float: right;
        position: absolute;
        text-align: center;
        position: absolute;
        z-index: 100;
        font-weight: 700;



    }

    .no_count {
        font-weight: 700;
        font-size: 24px;
    }


    .sub_missed {
        font-weight: 700;
        font-size: 14px;
    }

    .no_pec {
        font-weight: 700;
        font-size: 16px;
    }

    .card_sub_title {
        font-size: 14px;
    }

    .card_title {
        font-size: 16px;

    }
</style>
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">Appointment Dashboard</a></li>
        <li>
            As at {{ date('d M Y',strtotime("yesterday"));}}
        </li>

    </ul>
</div>
@if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control select2" id="partners" name="partner">
                        <option value="">All</option>
                        @if (count($partners) > 0)
                        @foreach($partners as $partner)
                        <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                        @endforeach
                        @endif
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option></option>
                        @if (count($counties) > 0)
                        @foreach($counties as $county)
                        <option value="{{$county->id }}">{{ ucwords($county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value="">All</option>
                        @if (count($sub_counties) > 0)
                        @foreach($sub_counties as $sub_county)
                        <option value="{{$sub_county->id }}">{{ ucwords($sub_county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="sites" name="site">
                        <option value=""> </option>
                        <option value="Paper Based">Paper Based</option>
                        <option value="EMR Based">EMR Based</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value=""></option>
                        @if (count($facilities) > 0)
                        @foreach($facilities as $facility)
                        <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="clinics" name="clinic">
                        <option value=""></option>
                        @if (count($clinics) > 0)
                        @foreach($clinics as $clinic)
                        <option value="{{$clinic->name }}">{{ ucwords($clinic->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="appointments" name="appointment">
                        <option value=""></option>
                        <option value="Missed">Missed</option>
                        <option value="Defaulted">Defaulted</option>
                        <option value="IIT">IIT</option>
                    </select>
                </div>
            </div>

            <div class='col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
@if (Auth::user()->access_level == 'County')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">

                    <select class="form-control select2" id="partners" name="partner">
                        <option></option>
                        @if (count($partners) > 0)
                        @foreach($partners as $partner)
                        <option value="{{$partner->id }}">{{ ucwords($partner->name) }}</option>
                        @endforeach
                        @endif
                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option value=""></option>
                        @if (count($counties) > 0)
                        @foreach($counties as $county)
                        <option value="{{$county->id }}">{{ ucwords($county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value=""></option>
                        @if (count($sub_counties) > 0)
                        @foreach($sub_counties as $sub_county)
                        <option value="{{$sub_county->id }}">{{ ucwords($sub_county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="sites" name="site">
                        <option value=""> </option>
                        <option value="Paper Based">Paper Based</option>
                        <option value="EMR Based">EMR Based</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value=""></option>
                        @if (count($facilities) > 0)
                        @foreach($facilities as $facility)
                        <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="clinics" name="clinic">
                        <option value=""></option>
                        @if (count($clinics) > 0)
                        @foreach($clinics as $clinic)
                        <option value="{{$clinic->name }}">{{ ucwords($clinic->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="appointments" name="appointment">
                        <option value=""></option>
                        <option value="Missed">Missed</option>
                        <option value="Defaulted">Defaulted</option>
                        <option value="IIT">IIT</option>
                    </select>
                </div>
            </div>

            <div class='col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-14">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-14">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
@if (Auth::user()->access_level == 'Partner')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="counties" name="county">
                        <option value="">All</option>
                        @if (count($counties) > 0)
                        @foreach($counties as $county)
                        <option value="{{$county->id }}">{{ ucwords($county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="subcounties" name="subcounty">
                        <option value="">All</option>
                        @if (count($sub_counties) > 0)
                        @foreach($sub_counties as $sub_county)
                        <option value="{{$sub_county->id }}">{{ ucwords($sub_county->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="sites" name="site">
                        <option value="">All</option>
                        <option value="Paper Based">Paper Based</option>
                        <option value="EMR Based">EMR Based</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value=""></option>
                        @if (count($facilities) > 0)
                        @foreach($facilities as $facility)
                        <option value="">All</option>
                        <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="clinics" name="clinic">
                        <option value=""></option>
                        @if (count($clinics) > 0)
                        @foreach($clinics as $clinic)
                        <option value="">All</option>
                        <option value="{{$clinic->name }}">{{ ucwords($clinic->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="appointments" name="appointment">
                        <option value="">All</option>
                        <option value="Missed">Missed</option>
                        <option value="Defaulted">Defaulted</option>
                        <option value="IIT">IIT</option>
                    </select>
                </div>
            </div>

            <div class='col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
@if (Auth::user()->access_level == 'Sub County')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="sites" name="site">
                        <option value=""> </option>
                        <option value="Paper Based">Paper Based</option>
                        <option value="EMR Based">EMR Based</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control select2" id="facilities" name="facility">
                        <option value=""></option>
                        @if (count($facilities) > 0)
                        @foreach($facilities as $facility)
                        <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="clinics" name="clinic">
                        <option value=""></option>
                        @if (count($clinics) > 0)
                        @foreach($clinics as $clinic)
                        <option value="{{$clinic->name }}">{{ ucwords($clinic->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="appointments" name="appointment">
                        <option value=""></option>
                        <option value="Missed">Missed</option>
                        <option value="Defaulted">Defaulted</option>
                        <option value="IIT">IIT</option>
                    </select>
                </div>
            </div>

            <div class='col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">

                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
@if (Auth::user()->access_level == 'Facility')
<div class="col">
    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="clinics" name="clinic">
                        <option value=""></option>
                        @if (count($clinics) > 0)
                        @foreach($clinics as $clinic)
                        <option value="{{$clinic->name }}">{{ ucwords($clinic->name) }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <select class="form-control input-rounded input-sm select2" id="appointments" name="appointment">
                        <option value=""></option>
                        <option value="Missed">Missed</option>
                        <option value="Defaulted">Defaulted</option>
                        <option value="IIT">IIT</option>
                    </select>
                </div>
            </div>
            <div class='col-lg-2'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="from" class="form-control" placeholder="From" name="from" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-12">
                            <input type="text" id="to" class="form-control" placeholder="To" name="to" max="{{date("Y-m-d")}}" onfocus="(this.type='date')" onkeydown="return false" />
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
@endif
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-dashboard-tab" data-toggle="tab" href="#nav-dashboard" role="tab" aria-controls="nav-dashboard" aria-selected="true">Appointment trends</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-appointment" role="tab" aria-selected="false">Missed Appointments</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-dashboard" role="tabpanel" aria-labelledby="nav-dashboard-tab">
        <div class="row">
            <div class="col-sm-4">
                <div class="TX_Curr card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_title">TX CURR</span>
                            <p id="tx_curr" class="no_count" style="margin-top:5px;"></p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Consented card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Clients Consented</span>
                            <p id="consented" class="no_count" style="margin-top:8px;"></p>
                            <div class="radial-01 radial-three-quarters">
                                <span id="percnt_consented" class="radial-01-number">
                                    <span class="radial-01-syb"><sup>%</sup></span>
                                </span>
                                <span class="radial-01-border-r"></span>
                                <span class="radial-01-border-l"></span>
                            </div>
                            <!-- <div class="box_pec" style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px;color:#006838;">
                                <h2 class="no_pec" id="percnt_consented" style="font-size: 10px; margin-top: 15px;"></h2>
                            </div> -->
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Booked card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Booked Appointments</span>
                            <p id="all_appointments" class="no_count" style="margin-top:8px;"></p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="Kept card o-hidden mb-2 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_title">Appointments Kept</span>
                            <p id="app_kept" class="no_count" style="margin-top:8px;"></p>
                            <div class="radial-01 radial-three-quarters">
                                <span id="percnt_kept" class="radial-01-number">
                                    <span class="radial-01-syb"><sup>%</sup></span>
                                </span>
                                <span class="radial-01-border-r"></span>
                                <span class="radial-01-border-l"></span>
                            </div>
                            <!-- <div class="box_pec" style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px; margin-bottom: 10px;">
                                <h2 class="no_pec" id="percnt_kept" style="font-size: 10px; margin-top: 15px;"></h2>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Not_Kept card o-hidden mb-2 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_title">Appointments Not Kept</span>
                            <p id="app_not_kept" class="no_count" style="margin-top:8px;"></p>
                            <div class="radial-01 radial-three-quarters">
                                <span id="percnt_not_kept" class="radial-01-number">
                                    <span class="radial-01-syb"><sup>%</sup></span>
                                </span>
                                <span class="radial-02-border-r"></span>
                                <span class="radial-01-border-l"></span>
                            </div>
                            <!-- <div class="box_pec" style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px; margin-bottom: 10px;">
                                <h2 class="no_pec" id="percnt_not_kept" style="font-size: 10px; margin-top: 15px;"></h2>
                            </div> -->
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Future card  o-hidden mb-2 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Future Appointments</span>
                            <p id="app_future" class="no_count" style="margin-top:5px;"></p>
                            <div class="radial-01 radial-three-quarters">
                                <span id="percnt_future" class="radial-01-number">
                                    <span class="radial-01-syb"><sup>%</sup></span>
                                </span>
                                <span class="radial-03-border-r"></span>
                                <span class="radial-01-border-l"></span>
                            </div>
                            <!-- <div class="box_pec" style="width: 3em; height: 3em; text-align: center; border-radius:50%; border:solid #fff 1px; box-shadow: 0 0 0 2px; padding:2px;">
                                <h2 class="no_pec" id="percnt_future" style="font-size: 10px; margin-top: 15px;"></h2>
                            </div> -->
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <div class="Indications card-body col-lg-12" id="maindiv">
            <p class="Indicator">Indicator Definition</p>
            <p class="Definition">Appointment Honored & Not Honored</p>
            <p class="Definitions">{{ json_encode($indicator_k[0]->description) }}</p>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-6">
                    <div class="card-body row">
                        <div id="appointment_gender" class="appointment_gender_chart" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
                <div class="col-6">

                    <div class="card-body row">
                        <div id="appointment_age" class="col" style="height:  400px;margin-top:20px;width: 900px;"></div> <br />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_marital" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <input id="authenticated" type="hidden" value="{{ auth()->user()->access_level }}">
        @if (Auth::user()->access_level == 'Sub County' || Auth::user()->access_level == 'Partner')

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @endif
        @if (Auth::user()->access_level == 'County')

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @endif
        @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_county" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="distribution_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>

        @endif

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Facility')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>

                            <div class="table-responsive">
                                <table id="table_client" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>NUPI No</th>
                                            <th>Clinic No</th>
                                            <th>Client Name</th>
                                            <th>DOB</th>
                                            <th>Phone No</th>
                                            <th>SMS Consent</th>
                                            <th>DSD Status</th>
                                            <th>Status</th>
                                            <th>Appointment Kept</th>
                                            <th>Appointment Not Kept</th>
                                        </tr>
                                    </thead>
                                    <tbody id="client">
                                        @if (count($client_list) > 0)
                                        @foreach($client_list as $result)
                                        <tr>

                                            <td> {{$result->upi_no}}</td>
                                            <td> {{$result->ccc_number}}</td>
                                            <td> {{$result->client_name}}</td>
                                            <td> {{$result->dob}}</td>
                                            <td> {{$result->phone_no}}</td>
                                            <td> {{$result->consented}}</td>
                                            <td> {{$result->dsd_status}}</td>
                                            <td> {{$result->client_status}}</td>
                                            <td> {{$result->kept_app}}</td>
                                            <td> {{$result->not_kept_app}}</td>

                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        @endif


                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- appointment tab -->
    <div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">


        <div class="row">
            <div class="col-sm-4">
                <div class="TX_Curr card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_title">Clients With Missed Appointment</span>
                            <p id="app_missed" class="no_count" style="margin-top:5px;"></p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Consented card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Consented Clients Who Missed</span>
                            <p id="consent_app" class="no_count" style="margin-top:5px;"></p>
                        </div>
                        <div class="h-45" style="float:right; margin-right: 20px;">
                            <div class="row">
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Missed</span>
                                    <p class="sub_missed" id="consent_missed"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Defaulted</span>
                                    <p class="sub_missed" id="consent_defaulted"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">LTFU</span>
                                    <p class="sub_missed" id="consent_iit"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Booked card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Clients Who Received SMS</span>
                            <p id="sms" class="no_count" style="margin-top:5px;"></p>
                        </div>
                        <div class="h-45" style="float:right; margin-right: 20px;">
                            <div class="row">
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Missed</span>
                                    <p class="sub_missed" id="sms_missed"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div style="margin-right: 20px;">
                                    <span class="card_sub_title">Defaulted</span>
                                    <p class="sub_missed" id="sms_defaulted"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div style="margin-right: 20px;">
                                    <span class="card_sub_title">LTFU</span>
                                    <p class="sub_missed" id="sms_iit"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="Kept card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content">
                            <span class="card_title">Clients Called</span>
                            <p id="contacted" class="no_count" style="margin-top:5px;"></p>
                        </div>

                        <div class="h-45" style="float:right;">
                            <div class="row">
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Missed</span>
                                    <p class="sub_missed" id="contacted_missed"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px; ">
                                    <span class="card_sub_title">Defaulted</span>
                                    <p class="sub_missed" id="contacted_defaulted"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">LTFU</span>
                                    <p class="sub_missed" id="contacted_iit"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Not_Kept card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Clients Physically Traced</span>
                            <p id="traced" class="no_count" style="margin-top:5px;"></p>
                        </div>
                        <div class="h-45" style="float:right;">
                            <div class="row">
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Missed</span>
                                    <p id="traced_missed" class="sub_missed" style="margin-top: 5px;"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Defaulted</span>
                                    <p id="traced_defaulted" class="sub_missed" style="margin-top: 5px;"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">LTFU</span>
                                    <p id="traced_iit" class="sub_missed" style="margin-top: 5px;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="Future card o-hidden mb-0 h-75">
                    <div class="card-body">
                        <div class="content" id="maindiv">
                            <span class="card_title">Clients Who RTC</span>
                            <p id="outcome" class="no_count" style="margin-top:5px;"></p>

                        </div>
                        <div class="h-35" style="float:right;">
                            <div class="row">
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Missed</span>
                                    <p class="sub_missed" id="outcome_missed"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">Defaulted</span>
                                    <p class="sub_missed" id="outcome_defaulted"></p>
                                </div>
                                <div class="" style="margin-right: 10px; border-left: 1px solid;"></div>
                                <div class="" style="margin-right: 20px;">
                                    <span class="card_sub_title">LTFU</span>
                                    <p class="sub_missed" id="outcome_iit"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="Indications card-body col-lg-12" id="maindiv">
            <p class="Indicator">Indicator Definition</p>
            <p class="Definition">Missed Appointments</p>
            <p class="Definitions">{{ json_encode($indicator_m[0]->description) }}</p>
        </div>


        <div class="col-md-12">
            <div class="row">
                <div class="col-6">
                    <div class="card-body row">

                        <div id="missed_gender" name="missed_gender" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
                <div class="col-6">

                    <div class="card-body row">
                        <div id="missed_age" name="missed_age" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_marital" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_rate" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_period" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @if (Auth::user()->access_level == 'Sub County' || Auth::user()->access_level == 'Partner')

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_facility" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @endif
        @if (Auth::user()->access_level == 'County')

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @endif
        @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_county" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card-body row">
                        <div id="missed_partner" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                    </div>
                </div>


            </div>
        </div>
        @endif

        <div class="col-md-12">
            <div class="row">

                <div class="col-12">
                    <div class="card text-left">
                        @if (Auth::user()->access_level == 'Facility')
                        <div class="card-body">
                            <h4 class="card-title mb-3"></h4>
                            <div class="table-responsive">
                                <table id="table_missed" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>NUPI No</th>
                                            <th>Clinic No</th>
                                            <th>Client Name</th>
                                            <th>DOB</th>
                                            <th>Phone No</th>
                                            <th>SMS Consent</th>
                                            <th>DSD Status</th>
                                            <th>Status</th>
                                            <th>No of Days Missed</th>
                                            <th>Outcome</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($client_app_list) > 0)
                                        @foreach($client_app_list as $result)
                                        <tr>

                                            <td> {{$result->upi_no}}</td>
                                            <td> {{$result->ccc_number}}</td>
                                            <td> {{$result->client_name}}</td>
                                            <td> {{$result->dob}}</td>
                                            <td> {{$result->phone_no}}</td>
                                            <td> {{$result->consented}}</td>
                                            <td> {{$result->dsd_status}}</td>
                                            <td> {{$result->client_status}}</td>
                                            <td> {{$result->days_defaulted}}</td>
                                            <td> {{$result->final_outcome}}</td>

                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>

                                </table>

                            </div>

                        </div>
                        @endif


                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
<!-- <div id="dashboard_loader">
    <img style="  position:absolute;
        top:50px;
        left:0;
        right:0;
        bottom: 0px;
        width: 2000px;
        height: 2000px;
        margin:auto;" src="{{url('/images/loader.gif')}}" alt="loader" />

</div> -->

@endsection

@section('page-js')



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>



<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>


<script type="text/javascript">
    $("select").select2();
    $("#partners").select2({
        width: 'element',
        placeholder: "Select Partner"
    });
    $("#counties").select2({
        placeholder: "Select County"
    });
    $("#subcounties").select2({
        placeholder: "Select SubCounty"
    });
    $("#facilities").select2({
        placeholder: "Select Facility"
    });
    $("#clinics").select2({
        placeholder: "Select Clinic"
    });
    $("#appointments").select2({
        placeholder: "Appointment Status"
    });
    $("#sites").select2({
        placeholder: "Site Type"
    });



    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_dashboard_counties/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="county"]').empty();
                        $('select[name="county"]').append('<option value="">Please County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="county"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="county"]').empty();
            }
        });
    });


    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_dashboard_sub_counties/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="subcounty"]').empty();
                        $('select[name="subcounty"]').append('<option value="">Please Select Sub County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="subcounty"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="subcounty"]').on('change', function() {
            var subcountyID = $(this).val();
            if (subcountyID) {
                $.ajax({
                    url: '/get_dashboard_facilities/' + subcountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });
    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_county_facilities/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_partner_sub_counties/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="subcounty"]').empty();
                        $('select[name="subcounty"]').append('<option value="">Please Select Sub County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="subcounty"]').empty();
            }
        });
    });
    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_partner_facilities/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });
    $(document).ready(function() {
        $('select[name="site"]').on('change', function() {
            var siteID = $(this).val();
            if (siteID) {
                $.ajax({
                    url: '/get_sitetype_facilities/' + siteID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });

    $('#table_client').DataTable({
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
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'csv',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'excel',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'pdf',
                title: 'Clients List',
                filename: 'Clients List'
            },
            {
                extend: 'print',
                title: 'Clients List',
                filename: 'Clients List'
            }
        ]
    });

    let authenticated = $('#authenticated').val();
    // console.log(authenticated);


    Swal.fire({
        title: "Please wait, loading...",
        showConfirmButton: false,
        allowOutsideClick: false
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'GET',
        url: "{{ route('appointment_data') }}",
        success: function(data) {
            const consent = data.consented_clients;
            const apps = data.all_appoinments;
            const tx = data.all_tx_curr;
            const missed = data.client_missed;
            console.log(missed);
            appGender(data.appointment_gender);
            appAge(data.appointment_age);
            appMarital(data.appointment_marital);

            missedAge(data.missed_age);
            missedGender(data.missed_gender);
            missedMarital(data.missed_marital);
            if (authenticated == 'Facility') {

                // $.each(data.client_list, function(key, value) {
                //     $('#client').append("<tr>\
                // 						<td>" + value.upi_no + "</td>\
                // 						<td>" + value.ccc_number + "</td>\
                // 						<td>" + value.client_name + "</td>\
                //                         <td>" + value.dob + "</td>\
                //                         <td>" + value.phone_no + "</td>\
                //                         <td>" + value.consented + "</td>\
                //                         <td>" + value.dsd_status + "</td>\
                //                         <td>" + value.client_status + "</td>\
                //                         <td>" + value.kept_app + "</td>\
                //                         <td>" + value.not_kept_app + "</td>\
                // 						</tr>");
                // })
            }
            if (authenticated == 'Admin' || authenticated == 'Donor') {
                appCounty(data.appointment_county);
                appPartner(data.appointment_partner)
                missedCounty(data.missed_county);
                missedPartner(data.missed_partner);
            }
            if (authenticated == 'County') {
                appPartner(data.appointment_partner)
                missedPartner(data.missed_partner);
            }
            if (authenticated == 'Partner' || authenticated == 'Sub County') {
                appFacility(data.appointment_facility)
                missedFacility(data.missed_facility)
            }
            missedPeriod(data.app_period);
            missedRate(data.app_period);


            for (var x = 0; x < consent.length; x++) {
                consented = parseInt(consent[x].consented);
                console.log(consented);
                if (consented == undefined || consented == null) {
                    consented = 0;
                } else {
                    consented = consented.toLocaleString();
                }
                percnt_consented = Math.round(consent[x].percent_consented).toFixed(0) + '%';
            }
            for (var x = 0; x < apps.length; x++) {
                all_appointments = apps[x].total_app;
                console.log(all_appointments);
                if (all_appointments == undefined || all_appointments == null) {
                    all_appointments = 0;
                } else {
                    all_appointments = all_appointments.toLocaleString();
                }
                app_kept = apps[x].kept_app;
                if (app_kept == undefined || app_kept == null) {
                    app_kept = 0;
                } else {
                    app_kept = app_kept.toLocaleString();
                }
                app_not_kept = apps[x].not_kept_app;
                if (app_not_kept == undefined || app_not_kept == null) {
                    app_not_kept = 0;
                } else {
                    app_not_kept = app_not_kept.toLocaleString();
                }
                app_future = apps[x].future;
                if (app_future == undefined || app_future == null) {
                    app_future = 0;
                } else {
                    app_future = app_future.toLocaleString();
                }
                sms_sent = apps[x].messages;
                if (sms_sent == undefined || sms_sent == null) {
                    sms_sent = 0;
                } else {
                    sms_sent = sms_sent.toLocaleString();
                }
                percnt_kept = Math.round(apps[x].percent_kept).toFixed(1) + '%';
                percnt_not_kept = Math.round(apps[x].percent_not_kept).toFixed(1) + '%';
                percnt_future = Math.round(apps[x].percent_future).toFixed(1) + '%';
            }
            for (var x = 0; x < tx.length; x++) {
                tx_curr = parseInt(tx[x].tx_cur).toLocaleString();

            }
            let app_missed = 0;
            let consent_app = 0;
            let consent_missed = 0;
            let consent_defaulted = 0;
            let consent_iit = 0;
            let sms = 0;
            let sms_missed = 0;
            let sms_defaulted = 0;
            let sms_iit = 0;
            let contacted = 0;
            let contacted_missed = 0;
            let contacted_defaulted = 0;
            let contacted_iit = 0;
            let traced = 0;
            let traced_missed = 0;
            let traced_defaulted = 0;
            let traced_iit = 0;
            let outcome = 0;
            let outcome_missed = 0;
            let outcome_defaulted = 0;
            let outcome_iit = 0;
            for (var x = 0; x < missed.length; x++) {
                if (missed[x].not_kept_app > 0) {
                    app_missed++;
                } else {
                    app_missed;
                }
                if (missed[x].consent > 0) {
                    consent_app++;
                } else {
                    consent_app;
                }
                if (missed[x].missed_consent > 0) {
                    consent_missed++;
                } else {
                    consent_missed;
                }
                if (missed[x].defaulted_consent > 0) {
                    consent_defaulted++;
                } else {
                    consent_defaulted;
                }
                if (missed[x].iit_consent > 0) {
                    consent_iit++;
                } else {
                    consent_iit;
                }
                if (missed[x].messages > 0) {
                    sms++;
                } else {
                    sms;
                }
                if (missed[x].missed_messages > 0) {
                    sms_missed++;
                } else {
                    sms_missed;
                }
                if (missed[x].defaulted_messages > 0) {
                    sms_defaulted++;
                } else {
                    sms_defaulted;
                }
                if (missed[x].iit_messages > 0) {
                    sms_iit++;
                } else {
                    sms_iit;
                }
                if (missed[x].called > 0) {
                    contacted++;
                } else {
                    contacted;
                }
                if (missed[x].missed_called > 0) {
                    contacted_missed++;
                } else {
                    contacted_missed;
                }
                if (missed[x].defaulted_called > 0) {
                    contacted_defaulted++;
                } else {
                    contacted_defaulted;
                }
                if (missed[x].iit_called > 0) {
                    contacted_iit++;
                } else {
                    contacted_iit;
                }
                if (missed[x].physically_traced > 0) {
                    traced++;
                } else {
                    traced;
                }
                if (missed[x].missed_traced > 0) {
                    traced_missed++;
                } else {
                    traced_missed;
                }
                if (missed[x].defaulted_traced > 0) {
                    traced_defaulted++;
                } else {
                    traced_defaulted;
                }
                if (missed[x].iit_traced > 0) {
                    traced_iit++;
                } else {
                    traced_iit;
                }
                if (missed[x].final_outcome > 0) {
                    outcome++;
                } else {
                    outcome;
                }
                if (missed[x].missed_outcome > 0) {
                    outcome_missed++;
                } else {
                    outcome_missed;
                }
                if (missed[x].defaulted_outcome > 0) {
                    outcome_defaulted++;
                } else {
                    outcome_defaulted;
                }
                if (missed[x].iit_outcome > 0) {
                    outcome_iit++;
                } else {
                    outcome_iit;
                }

            }
            $("#tx_curr").html(tx_curr);
            $("#consented").html(consented);
            $("#percnt_consented").html(percnt_consented);
            $("#all_appointments").html(all_appointments);
            $("#app_kept").html(app_kept);
            $("#app_not_kept").html(app_not_kept);
            $("#app_future").html(app_future);
            $("#sms_sent").html(sms_sent);
            $("#percnt_kept").html(percnt_kept);
            $("#percnt_not_kept").html(percnt_not_kept);
            $("#percnt_future").html(percnt_future);

            $("#app_missed").html(app_missed);
            $("#consent_app").html(consent_app);
            $("#consent_missed").html(consent_missed);
            $("#consent_defaulted").html(consent_defaulted);
            $("#consent_iit").html(consent_iit);

            $("#sms").html(sms);
            $("#sms_missed").html(sms_missed);
            $("#sms_defaulted").html(sms_defaulted);
            $("#sms_iit").html(sms_iit);

            $("#contacted").html(contacted);
            $("#contacted_missed").html(contacted_missed);
            $("#contacted_defaulted").html(contacted_defaulted);
            $("#contacted_iit").html(contacted_iit);

            $("#traced").html(traced);
            $("#traced_missed").html(traced_missed);
            $("#traced_defaulted").html(traced_defaulted);
            $("#traced_iit").html(traced_iit);

            $("#outcome").html(outcome);
            $("#outcome_missed").html(outcome_missed);
            $("#outcome_defaulted").html(outcome_defaulted);
            $("#outcome_iit").html(outcome_iit);
            Swal.close();
            // $("#dashboard_loader").hide();
        }
    });

    $('#dataFilter').on('submit', function(e) {
        e.preventDefault();
        // $("#dashboard_loader").show();
        let partners = $('#partners').val();
        let counties = $('#counties').val();
        let subcounties = $('#subcounties').val();
        let facilities = $('#facilities').val();
        let from = $('#from').val();
        let to = $('#to').val();
        let clinics = $('#clinics').val();
        let appointments = $('#appointments').val();
        let sites = $('#sites').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        Swal.fire({
            title: "Please wait, loading...",
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            type: 'GET',
            data: {
                "partners": partners,
                "counties": counties,
                "subcounties": subcounties,
                "facilities": facilities,
                "from": from,
                "to": to,
                "clinics": clinics,
                "appointments": appointments,
                "sites": sites
            },
            url: "{{ route('filter_app_data') }}",
            success: function(data) {

                const consent = data.consented_clients;
                const apps = data.all_appoinments;
                const tx = data.all_tx_curr;
                const client_app = data.client_app_list;
                console.log(client_app);
                const missed = data.client_missed;
                appGender(data.appointment_gender);
                appAge(data.appointment_age);
                appMarital(data.appointment_marital);

                missedAge(data.missed_age);
                missedGender(data.missed_gender);
                missedMarital(data.missed_marital);
                if (authenticated == 'Facility') {
                    // $('#table_client')[0].reset();
                    // $("tbody").html("");
                    // $.each(data.client_app_list, function(index, value) {
                    //     var row = $("<tr><td>" +
                    //         value.upi_no + "</td><td>" +
                    //         value.ccc_number + "</td><td>" +
                    //         value.dob + "</td><td>" +
                    //         value.phone_no + "</td><td>" +
                    //         value.consented + "</td><td>" +
                    //         value.dsd_status + "</td><td>" +
                    //         value.client_status + "</td><td>" +
                    //         value.kept_app + "</td><td>" +
                    //         value.not_kept_app + "</td><td>");

                    //     $("tbody").append(row);
                    // });
                    // $("#table_client").html(data.client_app_list);
                }
                if (authenticated == 'Admin' || authenticated == 'Donor') {
                    appCounty(data.appointment_county);
                    appPartner(data.appointment_partner)
                    missedCounty(data.missed_county);
                    missedPartner(data.missed_partner);
                }
                if (authenticated == 'County') {
                    appPartner(data.appointment_partner)
                    missedPartner(data.missed_partner);
                }
                if (authenticated == 'Partner' || authenticated == 'Sub County') {
                    appFacility(data.appointment_facility)
                    missedFacility(data.missed_facility)
                }
                missedPeriod(data.app_period);
                const tests = data.app_period;
                missedRate(data.app_period);
                console.log(tests);

                for (var x = 0; x < consent.length; x++) {
                    consented = consent[x].consented;
                    console.log(consented);
                    if (consented == undefined || consented == null) {
                        consented = 0;
                    } else {
                        consented = parseInt(consented);
                    }
                    percnt_consented = Math.round(consent[x].percent_consented).toFixed(1) + '%';
                }
                for (var x = 0; x < apps.length; x++) {
                    all_appointments = apps[x].total_app;
                    if (all_appointments == undefined || all_appointments == null) {
                        all_appointments = 0;
                    } else {
                        all_appointments = all_appointments.toLocaleString();
                    }
                    app_kept = apps[x].kept_app;
                    if (app_kept == undefined || app_kept == null) {
                        app_kept = 0;
                    } else {
                        app_kept = app_kept.toLocaleString();
                    }
                    app_not_kept = apps[x].not_kept_app;
                    if (app_not_kept == undefined || app_not_kept == null) {
                        app_not_kept = 0;
                    } else {
                        app_not_kept = app_not_kept.toLocaleString();
                    }
                    app_future = apps[x].future;
                    if (app_future == undefined || app_future == null) {
                        app_future = 0;
                    } else {
                        app_future = app_future.toLocaleString();
                    }
                    sms_sent = apps[x].messages;
                    if (sms_sent == undefined || sms_sent == null) {
                        sms_sent = 0;
                    } else {
                        sms_sent = sms_sent.toLocaleString();
                    }

                    percnt_kept = Math.round(apps[x].percent_kept).toFixed(1) + '%';
                    percnt_not_kept = Math.round(apps[x].percent_not_kept).toFixed(1) + '%';
                    percnt_future = Math.round(apps[x].percent_future).toFixed(1) + '%';
                }
                for (var x = 0; x < tx.length; x++) {
                    tx_curr = tx[x].tx_cur
                    if (tx_curr == undefined || tx_curr == null || isNaN(tx_curr)) {
                        tx_curr = 0;
                    } else {
                        tx_curr = tx_curr.toLocaleString();
                    }

                }
                let app_missed = 0;
                let consent_app = 0;
                let consent_missed = 0;
                let consent_defaulted = 0;
                let consent_iit = 0;
                let sms = 0;
                let sms_missed = 0;
                let sms_defaulted = 0;
                let sms_iit = 0;
                let contacted = 0;
                let contacted_missed = 0;
                let contacted_defaulted = 0;
                let contacted_iit = 0;
                let traced = 0;
                let traced_missed = 0;
                let traced_defaulted = 0;
                let traced_iit = 0;
                let outcome = 0;
                let outcome_missed = 0;
                let outcome_defaulted = 0;
                let outcome_iit = 0;
                for (var x = 0; x < missed.length; x++) {
                    if (missed[x].not_kept_app > 0) {
                        app_missed++;
                    } else {
                        app_missed;
                    }
                    if (missed[x].consent > 0) {
                        consent_app++;
                    } else {
                        consent_app;
                    }
                    if (missed[x].missed_consent > 0) {
                        consent_missed++;
                    } else {
                        consent_missed;
                    }
                    if (missed[x].defaulted_consent > 0) {
                        consent_defaulted++;
                    } else {
                        consent_defaulted;
                    }
                    if (missed[x].iit_consent > 0) {
                        consent_iit++;
                    } else {
                        consent_iit;
                    }
                    if (missed[x].messages > 0) {
                        sms++;
                    } else {
                        sms;
                    }
                    if (missed[x].missed_messages > 0) {
                        sms_missed++;
                    } else {
                        sms_missed;
                    }
                    if (missed[x].defaulted_messages > 0) {
                        sms_defaulted++;
                    } else {
                        sms_defaulted;
                    }
                    if (missed[x].iit_messages > 0) {
                        sms_iit++;
                    } else {
                        sms_iit;
                    }
                    if (missed[x].called > 0) {
                        contacted++;
                    } else {
                        contacted;
                    }
                    if (missed[x].missed_called > 0) {
                        contacted_missed++;
                    } else {
                        contacted_missed;
                    }
                    if (missed[x].defaulted_called > 0) {
                        contacted_defaulted++;
                    } else {
                        contacted_defaulted;
                    }
                    if (missed[x].iit_called > 0) {
                        contacted_iit++;
                    } else {
                        contacted_iit;
                    }
                    if (missed[x].physically_traced > 0) {
                        traced++;
                    } else {
                        traced;
                    }
                    if (missed[x].missed_traced > 0) {
                        traced_missed++;
                    } else {
                        traced_missed;
                    }
                    if (missed[x].defaulted_traced > 0) {
                        traced_defaulted++;
                    } else {
                        traced_defaulted;
                    }
                    if (missed[x].iit_traced > 0) {
                        traced_iit++;
                    } else {
                        traced_iit;
                    }
                    if (missed[x].final_outcome > 0) {
                        outcome++;
                    } else {
                        outcome;
                    }
                    if (missed[x].missed_outcome > 0) {
                        outcome_missed++;
                    } else {
                        outcome_missed;
                    }
                    if (missed[x].defaulted_outcome > 0) {
                        outcome_defaulted++;
                    } else {
                        outcome_defaulted;
                    }
                    if (missed[x].iit_outcome > 0) {
                        outcome_iit++;
                    } else {
                        outcome_iit;
                    }

                }
                $("#tx_curr").html(tx_curr);
                $("#consented").html(consented);
                $("#percnt_consented").html(percnt_consented);
                $("#all_appointments").html(all_appointments);
                $("#app_kept").html(app_kept);
                $("#app_not_kept").html(app_not_kept);
                $("#app_future").html(app_future);
                $("#sms_sent").html(sms_sent);
                $("#percnt_kept").html(percnt_kept);
                $("#percnt_not_kept").html(percnt_not_kept);
                $("#percnt_future").html(percnt_future);

                $("#app_missed").html(app_missed);
                $("#consent_app").html(consent_app);
                $("#consent_missed").html(consent_missed);
                $("#consent_defaulted").html(consent_defaulted);
                $("#consent_iit").html(consent_iit);

                $("#sms").html(sms);
                $("#sms_missed").html(sms_missed);
                $("#sms_defaulted").html(sms_defaulted);
                $("#sms_iit").html(sms_iit);

                $("#contacted").html(contacted);
                $("#contacted_missed").html(contacted_missed);
                $("#contacted_defaulted").html(contacted_defaulted);
                $("#contacted_iit").html(contacted_iit);

                $("#traced").html(traced);
                $("#traced_missed").html(traced_missed);
                $("#traced_defaulted").html(traced_defaulted);
                $("#traced_iit").html(traced_iit);

                $("#outcome").html(outcome);
                $("#outcome_missed").html(outcome_missed);
                $("#outcome_defaulted").html(outcome_defaulted);
                $("#outcome_iit").html(outcome_iit);

                console.log(consented);
                Swal.close();
                // $("#dashboard_loader").hide();

            }
        });

    });



    $('#table_missed').DataTable({
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
                title: 'Missed Appointment List',
                filename: 'Missed Appointment List'
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'all'
                    }
                },
                title: 'Missed Appointment List',
                filename: 'Missed Appointment List'
            }, 'excel', 'pdf', 'print'
        ]
    });

    function appAge(data) {
        let age_group = [];
        let kept_app = [];
        let not_kept_app = [];
        let total = [];
        let percent_kept = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            age_group.push(data[i].age_group);

            not_kept_app.push(data[i].not_kept_app)
            parseFloat(percent_kept.push(data[i].percent_kept)).toFixed(1);
            parseFloat(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('appointment_age', {
            chart: {
                type: 'column',
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Appointment Distribution by Age',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },

            xAxis: {
                categories: age_group,
                crosshair: true
            },
            yAxis: {
                max: 100,
                title: {
                    useHTML: true,
                    text: 'Percentage'
                }
            },
            tooltip: {

                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Kept',
                data: percent_kept,
                color: '#01058A'

            }, {
                name: 'Not Kept',
                data: percent_not_kept,
                color: '#97080F'

            }]
        });
    }

    function appGender(data) {
        let gender = [];
        let percent_kept = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            gender.push(data[i].gender);
            Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('appointment_gender', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            style: {
                fontFamily: 'Inter'
            },
            title: {
                text: 'Appointment Distribution by Gender',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },

            xAxis: {
                categories: gender,
                crosshair: true
            },
            yAxis: {
                max: 100,
                title: {
                    useHTML: true,
                    text: 'Percentage'
                }
            },
            tooltip: {

                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Kept',
                color: '#01058A',
                data: percent_kept
            }, {
                name: 'Not Kept',
                color: '#97080F',
                data: percent_not_kept

            }]
        });
    }

    function appMarital(data) {
        let marital = [];
        let percent_kept = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            marital.push(data[i].marital);
            Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('distribution_marital', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Appointment Distribution by Marital Status',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: marital,

            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage'
                },

                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: ( // theme
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || 'gray'
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Kept',
                color: '#01058A',
                data: percent_kept

            }, {
                name: 'Not Kept',
                color: '#97080F',
                data: percent_not_kept

            }],

        });
    }

    function missedGender(data) {
        let gender = [];
        let percent_rtc = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            gender.push(data[i].gender);
            Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('missed_gender', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Missed Appointment Distribution by Gender',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: gender,
                crosshair: true
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage '
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                }
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'Missed',
                color: '#97080F',
                data: percent_not_kept

            }, {
                name: 'Returned to care',
                color: '#01058A',
                data: percent_rtc

            }]
        });
    }

    function missedAge(data) {
        let age_group = [];
        let percent_rtc = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            age_group.push(data[i].age_group);
            Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('missed_age', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Missed Appointment Distribution by Age',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: age_group,
                crosshair: true
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage '
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                }
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'Missed',
                color: '#97080F',
                data: percent_not_kept

            }, {
                name: 'Returned to care',
                color: '#01058A',
                data: percent_rtc

            }]
        });
    }

    function missedMarital(data) {
        let marital = [];
        let percent_rtc = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            marital.push(data[i].marital);
            Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('missed_marital', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Missed Client Distribution by Marital Status',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: marital,
                crosshair: true
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage '
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                }
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'Missed',
                color: '#97080F',
                data: percent_not_kept

            }, {
                name: 'Returned to care',
                color: '#01058A',
                data: percent_rtc

            }]
        });
    }

    function missedPeriod(data) {
        let new_date = [];
        let percent_rtc = [];
        let percent_not_kept = [];
        for (let i = 0; i < data.length; i++) {
            new_date.push(data[i].new_date);
            Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
        }
        Highcharts.chart('missed_period', {
            chart: {
                type: 'column'
            },
            legend: {
                itemStyle: {
                    fontFamily: 'Inter',
                    fontSize: '12px'
                }
            },
            title: {
                text: 'Missed Client Distribution by Period',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: new_date,
                crosshair: true
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage '
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        textOutline: 'none'
                    }
                }
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>' +
                        this.series.name + ': ' + this.y + '%';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'Missed',
                color: '#97080F',
                data: percent_not_kept

            }, {
                name: 'Returned to care',
                color: '#01058A',
                data: percent_rtc

            }]
        });
    }

    function missedRate(data) {
        let new_date = [];
        let percent_rtc = [];
        let percent_not_kept = [];
        let total_app = [];
        for (let i = 0; i < data.length; i++) {
            new_date.push(data[i].new_date);
            Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
            Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            total_app.push(data[i].total_app);
        }
        Highcharts.chart('missed_rate', {
            title: {
                text: 'Missed Rate by Period'
            },
            xAxis: {
                categories: new_date
            },
            yAxis: [{
                title: {
                    text: 'Number of Appointments'
                }
            }, { // Secondary yAxis
                gridLineWidth: 0,
                alignTicks: false,
                min: 0,
                max: 100,
                title: {
                    text: 'Percentage',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} %',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            series: [{
                type: 'column',
                name: 'Appointments',
                color: '#01058A',
                data: total_app

            }, {
                type: 'spline',
                yAxis: 1,
                name: 'Missed Rate',
                data: percent_not_kept,
                color: '#97080F',
                tooltip: {
                    valueSuffix: '%'
                },
                marker: {
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[3],
                    fillColor: 'white'
                }
            }]
        });
    }


    if (authenticated == 'Admin' || authenticated == 'Donor') {
        function appCounty(data) {
            let county = [];
            let percent_kept = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                county.push(data[i].county);
                Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('distribution_county', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Appointment Distribution by County',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: county

                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Kept',
                    color: '#01058A',
                    data: percent_kept

                }, {
                    name: 'Not Kept',
                    color: '#97080F',
                    data: percent_not_kept

                }],

            });
        }

        function missedCounty(data) {
            let county = [];
            let percent_rtc = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                county.push(data[i].county);
                Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('missed_county', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Missed Client Distribution by County',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: county,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage '
                    },
                    stackLabels: {
                        enabled: false,
                        style: {
                            fontWeight: 'bold',
                            textOutline: 'none'
                        }
                    }
                },

                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: false
                        }
                    }
                },
                series: [{
                    name: 'Missed',
                    color: '#97080F',
                    data: percent_not_kept

                }, {
                    name: 'Returned to care',
                    color: '#01058A',
                    data: percent_rtc

                }]
            });
        }

        function missedPartner(data) {
            let partner = [];
            let percent_rtc = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                partner.push(data[i].partner);
                Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('missed_partner', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Missed Client Distribution by Partner',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: partner,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage '
                    },
                    stackLabels: {
                        enabled: false,
                        style: {
                            fontWeight: 'normal',
                            textOutline: 'none'
                        }
                    }
                },

                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: false
                        }
                    }
                },
                series: [{
                    name: 'Missed',
                    color: '#97080F',
                    data: percent_not_kept

                }, {
                    name: 'Returned to care',
                    color: '#01058A',
                    data: percent_rtc

                }]
            });
        }

        function appPartner(data) {
            let partner = [];
            let percent_kept = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                partner.push(data[i].partner);
                Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('distribution_partner', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Appointment Distribution by Partner',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: partner,
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Kept',
                    color: '#01058A',
                    data: percent_kept

                }, {
                    name: 'Not Kept',
                    color: '#97080F',
                    data: percent_not_kept

                }],

            });
        }
    }

    if (authenticated == 'County') {
        function missedPartner(data) {
            let partner = [];
            let percent_rtc = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                partner.push(data[i].partner);
                Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed();
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('missed_partner', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Missed Client Distribution by Partner',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: partner,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage '
                    },
                    stackLabels: {
                        enabled: false,
                        style: {
                            fontWeight: 'normal',
                            textOutline: 'none'
                        }
                    }
                },

                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: false
                        }
                    }
                },
                series: [{
                    name: 'Missed',
                    color: '#97080F',
                    data: percent_not_kept

                }, {
                    name: 'Returned to care',
                    color: '#01058A',
                    data: percent_rtc

                }]
            });
        }

        function appPartner(data) {
            let partner = [];
            let percent_kept = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                partner.push(data[i].partner);
                Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('distribution_partner', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Appointment Distribution by Partner',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: partner,
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Kept',
                    color: '#01058A',
                    data: percent_kept

                }, {
                    name: 'Not Kept',
                    color: '#97080F',
                    data: percent_not_kept

                }],

            });
        }
    }
    if (authenticated == 'Partner' || authenticated == 'Sub County') {
        function appFacility(data) {
            let facility = [];
            let percent_kept = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                facility.push(data[i].facility);
                Math.round(percent_kept.push(data[i].percent_kept)).toFixed(1);
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('distribution_facility', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Appointment Distribution by Facility',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: facility,
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Kept',
                    color: '#01058A',
                    data: percent_kept

                }, {
                    name: 'Not Kept',
                    color: '#97080F',
                    data: percent_not_kept

                }],

            });
        }

        function missedFacility(data) {
            let facility = [];
            let percent_rtc = [];
            let percent_not_kept = [];
            for (let i = 0; i < data.length; i++) {
                facility.push(data[i].facility);
                Math.round(percent_rtc.push(data[i].percent_rtc)).toFixed();
                Math.round(percent_not_kept.push(data[i].percent_not_kept)).toFixed(1);
            }
            Highcharts.chart('missed_facility', {
                chart: {
                    type: 'column'
                },
                legend: {
                    itemStyle: {
                        fontFamily: 'Inter',
                        fontSize: '12px'
                    }
                },
                title: {
                    text: 'Missed Appointment Distribution by Facility',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                },
                xAxis: {
                    categories: facility,
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                        text: 'Percentage'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '%';
                    }
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Missed',
                    color: '#97080F',
                    data: percent_not_kept

                }, {
                    name: 'Returned To Care',
                    color: '#01058A',
                    data: percent_rtc

                }],

            });
        }
    }
</script>


@endsection


<!-- end of col -->