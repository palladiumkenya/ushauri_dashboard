<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

@extends('layouts.master')


@section('main-content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3 text-center">New Broadcast To All Facilities Users</div>
                <form role="form" method="post" action="{{route('send-broadcast-user')}}">
                    {{ csrf_field() }}
                    <div class="row">

                        <div class="col-md-6 form-group mb-3">
                            <label>Select Facility</label>
                            <select class="selectpicker form-control" data-width="100%" id="mfl_code" name="mfl_code" multiple data-actions-box="true">
                                @if (count($p_facilities) > 0)
                                @foreach($p_facilities as $facility)
                                <option value="{{$facility->code }}">{{ ucwords($facility->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>


                        <div class="col-md-12 form-group mb-3">
                            <label for="message">Message</label>
                            <textarea class="form-control" rows="3" id="message" name="message" placeholder="message"> </textarea>
                        </div>


                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection