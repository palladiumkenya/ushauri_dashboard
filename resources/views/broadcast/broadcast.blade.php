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
                <div class="card-title mb-3 text-center">New Broadcast To All Facilities</div>
                <form role="form" method="post" action="{{route('send-broadcast')}}">
                    {{ csrf_field() }}
                    <div class="row">

                        <div class="col-md-6 form-group mb-3">
                            <label>Select Group</label>
                            <select class="selectpicker form-control" data-width="100%" id="groups" name="groups[]" multiple data-actions-box="true">
                                @if (count($groups) > 0)
                                @foreach($groups as $group)
                                <option value="{{$group->id }}">{{ ucwords($group->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label>Select Gender</label>
                            <select class="selectpicker form-control" data-width="100%" id="genders" name="genders[]" multiple data-actions-box="true">
                                @if (count($genders) > 0)
                                @foreach($genders as $gender)
                                <option value="{{$gender->id }}">{{ ucwords($gender->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='col-sm-6'>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="col-md-4">
                                        <label for="firstName1">Broadcast Date</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="date" required="" id="broadcast_date" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="broadcast_date" min="{{date("Y-m-d")}}">
                                    </div>
                                    <div class="input-group-append">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="txt_time">Broadcast Time</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="txt_time" name="txt_time" >
                                <option value="">Please select </option>

                                @if (count($time) > 0)
                                @foreach($time as $times)
                                <option value="{{$times->id }}">{{ ucwords($times->name) }}</option>
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