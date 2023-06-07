@extends('layouts.master')
@section('before-css')


@endsection

@section('main-content')
@include('sweetalert::alert')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="card-title mb-3">Add Partner</div>
                <form role="form" method="post" action="{{route('addpartner')}}">
                    {{ csrf_field() }}
                    <div class="row">
                        <input type="hidden" name="id" id="id">
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Partner Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Partner name">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Phone No</label>
                            <input type="text" required="" name="phone" pattern="^(([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+$" placeholder="Phone No should be 10 Digits " id="phone_no" class="input-rounded input-sm form-control phone_no" />
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Description</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="add_partner_type">Partner Type</label>
                            <select class="form-control dynamic" data-dependant="rolename" data-width="100%" id="partner_type" name="add_partner_type">
                                <option value="">Please select </option>
                                @if (count($partner_type) > 0)
                                @foreach($partner_type as $type)
                                <option value="{{$type->id }}">{{ ucwords($type->name) }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="firstName1">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Location">
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="email" required="" name="email" class=" input-rounded input-sm form-control e_mail" id="email" placeholder="Enter your Email" />
                        </div>
                        <div class="col-md-6 form-group mb-3" id="add_status_div">
                            <label>Status : </label>
                            <select name="status" class="form-control status" id="status" name="status">
                                <option value="">Please select</option>
                                <option value="Active">Active</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@section('page-js')




@endsection

@section('bottom-js')

@endsection