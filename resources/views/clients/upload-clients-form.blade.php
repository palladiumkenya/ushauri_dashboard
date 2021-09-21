@extends('layouts.master')
@section('main-content')
           <div class="breadcrumb">
                <ul>
                    <li><a href="">Clients / Bulk / Upload</a></li>
                </ul>
            </div>

            <div class="separator-breadcrumb border-top"></div>

            <div class="row">
        <h4 class="mb-4">
            Bulk Upload Clients to Ushauri From KEMR
        </h4>
        </div>
        <div class="row">
        <form action="{{ route('client-file-import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                <div class="custom-file text-left">
                    <input type="file" name="file" class="form-control">
                </div>
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Upload Clients</button>
                </div>

            </div>
        </form>



        </div>

        <div class="row">
        <h4 class="mb-4">
            Bulk Upload Clients to Ushauri From Template
        </h4>
        </div>

        <div class="row">
        <form action="{{ route('client-second-import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                <div class="custom-file text-left">
                    <input type="file" name="file" class="form-control">
                </div>
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Upload Clients</button>
                </div>

            </div>
        </form>



        </div>
        <div class="separator-breadcrumb border-top"></div>

<div class="row">

        <a class="btn btn-primary pull-right" href="{{ route('client-template-download') }}">Download Clients Template</a>
</div>
@endsection

@section('page-js')
     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
