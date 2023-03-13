<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('assets/images/ushauri.jpeg') }}">
    <title>Ushauri - Getting better one text at a time</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/css/bootstrap-select.min.css">
    @yield('before-css')
    {{-- theme css --}}








    @if (Session::get('layout') == 'vertical')
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-free-5.10.1-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/metisMenu.min.css') }}">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />

    @endif
    <link id="gull-theme" rel="stylesheet" href="{{ asset('assets\fonts\iconsmind\iconsmind.css') }}">
    <link id="gull-theme" rel="stylesheet" href="{{ asset('assets/styles/css/themes/lite-purple.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
    {{-- page specific css --}}
    @yield('page-css')
</head>






<body class="text-left">
    @php
    $layout = session('layout');
    @endphp

    <!-- Pre Loader Strat  -->
    <div class='loadscreen' id="preloader">

        <div class="loader spinner-bubble spinner-bubble-primary">


        </div>
    </div>
    <!-- Pre Loader end  -->







    <!-- ============ Compact Layout start ============= -->



    <!-- ============ Compact Layout End ============= -->





    <!-- ============ Horizontal Layout start ============= -->




    <!-- ============ Horizontal Layout End ============= -->




    <!-- ============ Vetical SIdebar Layout start ============= -->


    @include('layouts.large-vertical-sidebar.master')

    <!-- ============ Vetical SIdebar Layout End ============= -->




    <!-- ============ Large SIdebar Layout start ============= -->





    <!-- ============ Large Sidebar Layout End ============= -->






    <!-- ============Deafult  Large SIdebar Layout start ============= -->




    <!-- ============ Large Sidebar Layout End ============= -->




    <!-- ============ Search UI Start ============= -->
    {{-- @include('layouts.search') --}}
    <!-- ============ Search UI End ============= -->
    <script src="{{asset('assets/js/vendor/jquery-3.3.1.min.js') }}"></script>


    {{-- common js --}}
    <script src="{{ asset('assets/js/common-bundle-script.js') }}"></script>
    {{-- page specific javascript --}}
    @yield('page-js')

    {{-- theme javascript --}}
    {{-- <script src="{{ mix('assets/js/es5/script.js') }}"></script>
    --}}
    <script src="{{ asset('assets/js/script.js') }}"></script>


    @if ($layout == 'compact')
    <script src="{{ asset('assets/js/sidebar.compact.script.js') }}"></script>


    @elseif($layout=='normal')
    <script src="{{ asset('assets/js/sidebar.large.script.js') }}"></script>


    @elseif($layout=='horizontal')
    <script src="{{ asset('assets/js/sidebar-horizontal.script.js') }}"></script>
    @elseif($layout=='vertical')



    <script src="{{ asset('assets/js/tooltip.script.js') }}"></script>
    <script src="{{ asset('assets/js/es5/script_2.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/js/layout-sidebar-vertical.js') }}"></script>


    @else
    <script src="{{ asset('assets/js/sidebar.large.script.js') }}"></script>

    @endif

    <script src="{{ asset('assets/js/alerts.js') }}"></script>


        @if(session('status'))
        // alert('{{session('status')}}')
        swal({
            title: "{{session('status')}}",
            text: "{{session('texts')}}",
            icon: "{{session('statuscode')}}",
            button: "Ok",
        });
        @endif



    <script src="{{ asset('assets/js/customizer.script.js') }}"></script>

    {{-- laravel js --}}
    {{-- <script src="{{ mix('assets/js/laravel/app.js') }}"></script>
    --}}


    @yield('bottom-js')
</body>

</html>