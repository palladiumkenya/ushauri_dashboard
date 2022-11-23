@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">Client Messages</a></li>
        <li></li>
    </ul>
</div>
<form role="form" method="get" action="{{route('client_message')}}">
    {{ csrf_field() }}
    <div class="row">


        <div class='col'>
            <div class="form-group">
                <div class="input-group">
                    <div class="col-md-2">
                        <label for="firstName1">From</label>
                    </div>
                    <div class="col-md-10">
                        <input type="date" id="date_from" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="date_from" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="input-group-append">

                    </div>
                </div>
            </div>
        </div>

        <div class='col'>
            <div class="form-group">
                <div class="input-group">
                    <div class="col-md-2">
                        <label for="firstName1">To</label>
                    </div>
                    <div class="col-md-10">

                        <input type="date" id="date_to" class="form-control" placeholder="YYYY-mm-dd" name="date_to" max="{{date("Y-m-d")}}">
                    </div>
                    <div class="input-group-append">

                    </div>
                </div>
            </div>
        </div>


            <div class="col-sm-3">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

</form>








<!-- end of col -->

@endsection

@section('page-js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">

</script>


@endsection