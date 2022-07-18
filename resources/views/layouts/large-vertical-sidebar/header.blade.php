<div class="main-header">
    <div class="logo">
        <img class="pl-3" src="{{ asset('assets/images/ushauriNew_log.jpeg') }}" alt="ushauri">
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

    </div>


    <div class="header-part-right">
        <!-- Full screen toggle -->
        <img class=" pl-3" src="{{ asset('assets/images/NASCOP_Logo.png') }}" alt="ushauri">
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
<!-- header top menu end -->