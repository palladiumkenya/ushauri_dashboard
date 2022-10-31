@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">Clients / Bulk / Upload</a></li>
    </ul>
</div>

<div class="separator-breadcrumb border-top"></div>
<div class="col-md-6 form-group mb-3">
    <label>Upload From</label>
    <select class="form-control status" data-width="100%" id="section">
        <option value="">Please select upload type</option>
        <option value="kenyaemr">KenyaEMR Dataset</option>
        <option value="template">Ushauri Template</option>
    </select>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="upload" id="kenyaemr">
    <h6 class="mb-4">
        Bulk Upload Clients to Ushauri From KenyaEMR
    </h6>
    <form action="{{ route('client-file-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                <div class="custom-file text-left">
                    <input type="file" name="file" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Upload Clients</button>
            </div>

        </div>
    </form>

    <div class="separator-breadcrumb border-top"></div>

    <div class="row">

        <a class="btn btn-primary pull-right" href="{{ route('client-script-download') }}">Download Script</a>
    </div>

</div>


<div class="upload" id="template">
    <h6 class="mb-4">
        Bulk Upload Clients to Ushauri From Template
    </h6>
    <div class="separator-breadcrumb border-top"></div>
    <form action="{{ route('client-second-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
                <div class="custom-file text-left">
                    <input type="file" name="file" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Upload Clients</button>
            </div>

        </div>
    </form>


    <div class="separator-breadcrumb border-top"></div>

    <div class="row">

        <a class="btn btn-primary pull-right" href="{{ route('client-template-download') }}">Download Clients Template</a>
    </div>
</div>

@endsection

@section('page-js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('.upload').hide();
        $('#kenyaemr').show();
        $('#section').change(function() {
            $('.upload').hide();
            $('#' + $(this).val()).show();
        })
    });
</script>
<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
<script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection