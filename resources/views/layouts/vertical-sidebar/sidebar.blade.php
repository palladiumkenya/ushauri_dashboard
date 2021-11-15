<!-- start sidebar -->
<div class="sidebar-panel">
    <div class="gull-brand pr-3 text-center mt-4 mb-2 d-flex justify-content-center align-items-center">
        <img class="pl-3" src="{{ asset('assets/images/ushauri.jpeg') }}" alt="ushauri">
        <!-- <span class=" item-name text-20 text-primary font-weight-700">GULL</span> -->
        <div class="sidebar-compact-switch ml-auto"><span></span></div>

    </div>
    <!-- user -->
    <div class="scroll-nav" data-perfect-scrollbar data-suppress-scroll-x="true">

        <!-- user close -->
        <!-- side-nav start -->
        <div class="side-nav">

            <div class="main-menu">

                <!-- <ul>
                            <li>
                                <a href="*" class="active-color">
                                    <span class="item-name ">Dashboard</span>
                                    <span class="spacer"></span>
                                    <span class="item-badge badge badge-warning">100+</span>
                                </a>
                            </li>

                        </ul> -->
                <ul class="metismenu" id="menu">
                    <!-- <p class="main-menu-title text-muted ml-3 font-weight-700 text-13 mt-4 mb-2">Apps</p> -->
                    <li class="Ul_li--hover">
                        <a class="has-arrow" href="#">
                            <i class="i-Bar-Chart text-20 mr-2 text-muted"></i>
                            <span class="item-name  text-muted">Dashboard</span>
                        </a>
                        <ul class="mm-collapse">
                            <li class="item-name">
                                @if (Auth::user()->access_level == 'Facility')
                                <a class="" href="{{route('Reports-facility_home')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>
                                @endif
                                @if (Auth::user()->access_level == 'Admin')
                                <a class="" href="{{route('Reports-dashboard')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>
                                @endif
                                @if (Auth::user()->access_level == 'Partner')
                                <a class="" href="{{route('Reports-dashboard')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>
                                @endif
                            </li>
                            <li class="item-name">
                                <a class="" href="{{route('report-appointment-dashboard')}}">
                                    <span class=" text-muted">Appointments</span>
                                </a>
                            </li>
                            @if (Auth::user()->access_level == 'Admin')
                            <li class="item-name">
                                <a class="" href="{{route('report-IL-dashboard')}}">
                                    <span class=" text-muted">IL Extract</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    <li class="Ul_li--hover">
                        <a class="has-arrow" href="#">
                            <i class="i-Library text-20 mr-2 text-muted"></i>

                            <span class="item-name  text-muted">Clients</span>
                        </a>
                        <ul class="mm-collapse">
                            @if (Auth::user()->access_level == 'Facility')
                            <li class="item-name">
                                <a href="{{route('profile')}}">
                                    <span class="item-name">Client Profile</span>
                                </a>
                            </li>

                            <li class="item-name">
                                <a href="{{route('consent-clients')}}">
                                    <span class="item-name">Non Consented</span>
                                </a>
                            </li>

                            <li class="item-name">
                            <li class="nav-item">
                                <a href={{route('report-clients-list')}}>
                                    <span class="item-name">Clients</span>
                                </a>
                            </li>

                    </li>
                    <li class="item-name">
                        <a href="{{route('clients-extract')}}">
                            <span class="item-name">Client Extract</span>
                        </a>
                    </li>
                    @endif
                    @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner')
                    <li class="item-name">
                                <a href="{{route('Reports-clients_dashboard')}}">
                                    <span class="item-name">Clients</span>
                                </a>
                            </li>
                    <li class="item-name">
                        <a href={{route('upload-clients-form')}}>
                            <span class="item-name">Upload Clients</span>
                        </a>
                    </li>
                    @endif
                </ul>
                </li>
                @if (Auth::user()->access_level == 'Facility')
                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-Suitcase text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Appointments</span>
                    </a>
                    <ul class="mm-collapse">
                        <li class="item-name">
                            <a class="has-arrow cursor-pointer">
                                <span class="item-name">App Diary </span>

                            </a>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href="{{route('get_future_appointments')}}">
                                        <span class="item-name">Future</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="item-name">
                            <a class="has-arrow cursor-pointer">
                                <span class="item-name">Defaulter Diary </span>

                            </a>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href={{route('report-appointments-missed')}}>
                                        <span class="item-name">Missed</span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href={{route('report-appointments-defaulted')}}>
                                        <span class="item-name">Defaulted</span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href={{route('report-appointments-ltfu_clients')}}>
                                        <span class="item-name">Lost To Follow Up</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="item-name">
                            <a href="{{route('future-apps')}}">
                                <span class="item-name">Edit Appointment</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="{{route('report-appointments')}}">
                                <span class="item-name">Appointments</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="#">
                                <span class="item-name">Calender</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-Computer-Secure text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Wellness</span>
                    </a>
                    <ul class="mm-collapse">
                        <li class="item-name">
                            <a href={{route('report-ok_clients')}}>
                                <span class="item-name">Ok</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('report-not_ok_clients')}}>
                                <span class="item-name">Not Ok</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('report-unrecognised_response')}}>
                                <span class="item-name">Unrecognised</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- <p class="main-menu-title text-muted ml-3 font-weight-700 text-13 mt-4 mb-2">UI Elements</p> -->
                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-Computer-Secure text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Groups</span>
                    </a>
                    <ul class="mm-collapse">
                        <li class="item-name">
                            <a href={{route('report-adolescent_clients')}}>
                                <span class="item-name">Adolescent</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('report-pmtct_clients')}}>
                                <span class="item-name">PMTCT</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('report-adults_clients')}}>
                                <span class="item-name">Adult</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('report-paeds_clients')}}>
                                <span class="item-name">Paeds </span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (Auth::user()->access_level == 'Facility')
                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-Computer-Secure text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Tracing</span>
                    </a>
                    <ul class="mm-collapse">
                        <li class="item-name">
                            <a href={{route('clients-booked')}}>
                                <span class="item-name">Clients Tracing</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href={{route('admin-tracer-clients')}}>
                                <span class="item-name">Tracing List</span>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-File-Clipboard-File--Text text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Admin Tools</span>
                    </a>
                    <ul class="mm-collapse">
                        @if (Auth::user()->access_level == 'Admin')
                        <li class="item-name">
                            <a class="" href="{{route('admin-donors')}}">
                                <span class="item-name">Donor</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('admin-partners')}}">
                                <span class="item-name">Partner</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('admin-groups')}}">
                                <span class="item-name">Groups</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('admin_facilities')}}">
                                <span class="item-name">Facilities</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href={{route('admin-users')}}>
                                <span class="item-name">Users</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Content</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Module Mngr</span>
                            </a>
                        </li>

                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Sender</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Roles</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Language</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Notification Conf</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="">
                                <span class="item-name">Role Modules</span>
                            </a>
                        </li>

                        <li class="item-name">
                            <a class="" href="{{route('broadcast')}}">
                                <span class="item-name">Broadcast</span>
                            </a>
                        </li>

                        @endif
                        @if (Auth::user()->access_level == 'Facility')
                        <li class="item-name">
                            <a class="" href="{{route('clients-booked')}}">
                                <span class="item-name">Clients Tracing</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('broadcast')}}">
                                <span class="item-name">Broadcast</span>
                            </a>
                        </li>
                        @endif
                        @if (Auth::user()->access_level == 'Partner')
                        <li class="item-name">
                            <a class="" href={{route('admin-users')}}>
                                <span class="item-name">Users</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('broadcast')}}">
                                <span class="item-name">Broadcast</span>
                            </a>
                        </li>
                        @endif
                        @if (Auth::user()->access_level == 'Donor')
                        <li class="item-name">
                            <a class="" href="{{route('admin-partners')}}">
                                <span class="item-name">Partner</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('admin_facilities')}}">
                                <span class="item-name">Facilities</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href={{route('admin-users')}}>
                                <span class="item-name">Users</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('broadcast')}}">
                                <span class="item-name">Broadcast</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <li class="Ul_li--hover">
                    <a class="has-arrow" href="#">
                        <i class="i-File-Clipboard-File--Text text-20 mr-2 text-muted"></i>
                        <span class="item-name  text-muted">Reports</span>
                    </a>
                    <ul class="mm-collapse">
                        @if (Auth::user()->role_id == 12 || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin')

                        <li class="item-name">
                            <a class="" href="{{route('admin-tracer-clients')}}">
                                <span class="item-name">Client Tracer</span>
                            </a>
                        </li>
                        @endif


                        <li class="item-name">
                            <a class="has-arrow" href="#">
                                <span class="item-name  text-muted">DSD Reports</span>
                            </a>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a class="" href="{{route('Reports-dsd')}}">
                                        <span class="item-name">DSD Dairy</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="item-name">
                            <a class="has-arrow" href="#">
                                <span class="item-name  text-muted">PMTCT Reports</span>
                            </a>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href="{{route('report-pmtct-summary')}}">
                                        <span class="item-name">Summary</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a href="{{route('report-pmtct-appointment-dairy')}}">
                                        <span class="item-name">App Diary</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a href="{{route('report-pmtct-defaulter-dairy')}}">
                                        <span class="item-name">Defaulter Diary</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="item-name">
                            <a class="has-arrow" href="#">
                                <span class="item-name  text-muted">Hei Reports</span>
                            </a>
                            <ul class="mm-collapse">
                                <li class="item-name">
                                    <a href="{{route('report-hei-summary')}}">
                                        <span class="item-name">Summary</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a href="{{route('report-all_heis')}}">
                                        <span class="item-name">HEI List</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a class="" href="{{route('report-hei-appointment-dairy')}}">
                                        <span class="item-name">App Diary</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a href="{{route('report-hei-defaulter-dairy')}}">
                                        <span class="item-name">Defaulter Diary</span>
                                    </a>
                                </li>
                                <li class="item-name">
                                    <a href="{{route('report-hei-final-outcome')}}">
                                        <span class="item-name">Final Outcome</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')
                        <li class="item-name">
                            <a class="" href="{{route('broadcast')}}">
                                <span class="item-name">Broadcast</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('tracing-cost')}}">
                                <span class="item-name">Tracing Cost</span>
                            </a>
                        </li>
                        @endif

                        <li class="item-name">
                            <a href="{{route('report-lab_investigation')}}">
                                <span class="item-name">Lab Investigation</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a class="" href="{{route('my_facilities')}}">
                                <span class="item-name">My Facilities</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="{{route('report-deactivated_clients')}}">
                                <span class="item-name">Deactivated</span>
                            </a>
                        </li>

                        <li class="item-name">
                            <a href="{{route('report-transfer')}}">
                                <span class="item-name">Transfers</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="{{route('message-extract-report')}}">
                                <span class="item-name">Messages Extract</span>
                            </a>
                        </li>
                        @if (Auth::user()->access_level == 'Admin')
                        <li class="item-name">
                            <a href="{{route('access-report')}}">
                                <span class="item-name">User Report</span>
                            </a>
                        </li>
                        @endif
                        <li class="item-name">
                            <a href="{{route('report-consented')}}">
                                <span class="item-name">Consented Reports</span>
                            </a>
                        </li>
                        @if (Auth::user()->access_level == 'Admin')
                        <li class="item-name">
                            <a href="{{route('report-today_appointments')}}">
                                <span class="item-name">Todays Appointment</span>
                            </a>
                        </li>
                        @endif
                        <li class="item-name">
                            <a href="{{route('monthly-appointment-summary')}}">
                                <span class="item-name">Monthly Appointment</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="{{route('client-summary-report')}}">
                                <span class="item-name">Summary Report</span>
                            </a>
                        </li>
                        <li class="item-name">
                            <a href="{{route('tracing-outcome-report')}}">
                                <span class="item-name">Tracing OutCome</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="Ul_li--hover">
                    <a class="dropdown-item" href="{{route('logout')}}"><i class="nav-icon i-Power-3"></i>Log out</a>
                </li>

                </ul>
            </div>
        </div>
    </div>

    <!-- side-nav-close -->
</div>
<!-- end sidebar -->
<div class="switch-overlay"></div>