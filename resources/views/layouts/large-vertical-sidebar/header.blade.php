<div class="main-header">
    <input id="authenticated" type="hidden" value="{{ auth()->user()->access_level }}">
    <style>
        .reschedule-nishauri:hover {
            cursor: pointer;
        }
    </style>
    <div class="logo">
        <!-- <img src="{{ asset('assets/images/Ushauri_big.png') }}" alt="ushauri"> -->
        <img src="{{ asset('/assets/images/Ushauri_big.png') }}" style="margin-left: 30px;" height="40">
    </div>

    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="d-flex align-items-center">
        <!-- Mega menu -->
        <div class="dropdown mega-menu d-none d-md-block">


            <img class="pl-3" src="{{ asset('assets/images/MOH_New.png') }}" alt="ushauri">


        </div>
        <!-- / Mega menu -->
        <div class="">


        </div>
    </div>

    <div style="margin: auto">
        <!--
        @if (Auth::user()->access_level == 'Sub County')
        <h6 id="user"></h6>
        <h6 style="text-align:right;float:right; padding-left: 5px;">Sub County</h6>
        @elseif(Auth::user()->access_level == 'County')
        <h6 id="user" style="text-align:left;float:left;"></h6>
        <h6 style="text-align:right;float:right; padding-left: 5px;">County</h6>
        @elseif(Auth::user()->access_level == 'Partner')
        <h6 id="user"></h6>
        @elseif(Auth::user()->access_level == 'Facility')
        <h6 id="user"></h6>
        @else
        <h6>National Super Administrator</h6>
        @endif -->

    </div>


    <div class="header-part-right">


        @if (env('INSTANCE') === 'UshauriPublic')
        @if (Auth::user()->access_level == 'Facility')
        <!-- Full screen toggle -->
        <div class="reschedule-nishauri" style="position: relative; margin-right: 15px;">
            <span id="reschedule" class="badge rounded-pill badge-notification bg-danger" style="position: absolute; top: -10px; right: -10px; color: white;" title="Appointment Reschedule Requests"></span>
            <a href="{{route('reschedule_list')}}"><i class="fas fa-envelope fa-2x"></i></a>
        </div>

        <div class="reschedule-nishauri" style="position: relative;">
            <span id="reschedule" class="badge rounded-pill badge-notification bg-danger" style="position: absolute; top: -10px; right: -10px; color: white;" title="Drug Delivery Requests"></span>
            <a href="{{route('drug_delivery_list')}}"><i class="fas fa-prescription-bottle-alt fa-2x"></i></a>
        </div>

        @endif
        @endif

        <div>
            @if (env('INSTANCE') === 'UshauriPublic')
            <img class=" pl-3" src="{{ asset('assets/images/NASCOP_Logo.png') }}" alt="ushauri">
            @endif
            @if (env('INSTANCE') === 'UshauriDOD')
            <img class=" pl-3" src="{{ asset('assets/images/DOD_Logo.png') }}" alt="ushauri" height="50">
            @endif

        </div>


        <!-- Grid menu Dropdown -->
        <div class="dropdown widget_dropdown">


        </div>
        <!-- Notificaiton -->
        <div class="dropdown">


        </div>
        <!-- Notificaiton End -->

        <!-- User avatar dropdown -->
        <div class="dropdown">
            <div class="user col align-self-end">

                <img src="{{asset('assets/images/login/profile.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"> <b>{{{ isset(Auth::user()->f_name) ? Auth::user()->f_name : Auth::user()->l_name }}}</b></i>
                    </div>
                    <a class="dropdown-item" href="{{route('logout')}}">Sign Out</a>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" crossorigin="anonymous"> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>



<script type="text/javascript">
    let auth = $('#authenticated').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'GET',
        url: "{{ route('reschedule') }}",
        success: function(data) {
            // console.log(data.reschedule);

            if (auth == 'Facility') {
                $("#reschedule").html(data.reschedule);

            }
        }
    });
</script>
<!-- header top menu end -->
