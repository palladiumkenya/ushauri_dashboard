<div class="main-header">
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
        <!-- <input id="authenticated" type="hidden" value="{{ auth()->user()->access_level }}">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });
    // $.ajax({
    //     type: 'GET',
    //     url: "{{ route('user_info') }}",
    //     success: function(data) {
    //         if (authenticated == 'Sub County') {
    //             const user = data.user_info;
    //             for (var x = 0; x < user.length; x++) {
    //                 users = user[x].sub_county;
    //             }
    //             $("#user").html(users);
    //         }
    //         if (authenticated == 'County') {
    //             const user = data.user_info;
    //             for (var x = 0; x < user.length; x++) {
    //                 users = user[x].county;
    //             }
    //             $("#user").html(users);
    //         }
    //         if (authenticated == 'Facility') {
    //             const user = data.user_info;
    //             for (var x = 0; x < user.length; x++) {
    //                 users = user[x].facility;
    //             }
    //             $("#user").html(users);
    //         }
    //         if (authenticated == 'Partner') {
    //             const user = data.user_info;
    //             for (var x = 0; x < user.length; x++) {
    //                 users = user[x].partner;
    //                 console.log(users);
    //             }
    //             $("#user").html(users);
    //         }

    //     }
    // });
</script>
<!-- header top menu end -->