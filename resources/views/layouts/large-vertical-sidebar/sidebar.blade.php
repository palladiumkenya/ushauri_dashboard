<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">

            <li class="nav-item {{ request()->is('dashboard/*') ? 'active' : '' }}" data-item="dashboard">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('clients/*') ? 'active' : '' }}" data-item="clients">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Administrator"></i>
                    <span class="nav-text">Clients</span>
                </a>
                <div class="triangle"></div>
            </li>
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
            <li class="nav-item {{ request()->is('main_appointments/*') ? 'active' : '' }}" data-item="main_appointments">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Clock"></i>
                    <span class="nav-text">Appointments</span>
                </a>
                <div class="triangle"></div>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Facility')
            <li class="nav-item {{ request()->is('appointments/*') ? 'active' : '' }}" data-item="appointments">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Clock"></i>
                    <span class="nav-text">Appointments</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('wellness/*') ? 'active' : '' }}" data-item="wellness">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Computer-Secure"></i>
                    <span class="nav-text">Wellness</span>
                </a>
                <div class="triangle"></div>
            </li>
            <!-- <li class="nav-item {{ request()->is('groups/*') ? 'active' : '' }}" data-item="groups">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Business-Mens"></i>
                    <span class="nav-text">Groups</span>
                </a>
                <div class="triangle"></div>
            </li> -->

            <li class="nav-item {{ request()->is('tracing/*') ? 'active' : '' }}" data-item="tracing">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Find-User"></i>
                    <span class="nav-text">Tracing</span>
                </a>
                <div class="triangle"></div>
            </li>

            @endif
            <li class="nav-item {{ request()->is('admin/*') ? 'active' : '' }}" data-item="admin">
                <a class="nav-item-hold" href="">
                    <i class="nav-icon i-Double-Tap"></i>
                    <span class="nav-text">Admin Tools</span>
                </a>
                <div class="triangle"></div>
            </li>
            <!-- <li class="nav-item {{ request()->is('dsd/*') ? 'active' : '' }}" data-item="dsd">
                <a class="nav-item-hold" href="">
                    <i class="nav-icon i-Clock-3"></i>
                    <span class="nav-text">DSD Report</span>
                </a>
                <div class="triangle"></div>
            </li> -->
            <!-- <li class="nav-item {{ request()->is('pmtct/*') ? 'active' : '' }}" data-item="pmtct">
                <a class="nav-item-hold" href="">
                    <i class="nav-icon i-Conference"></i>
                    <span class="nav-text">PMTCT Reports</span>
                </a>
                <div class="triangle"></div>
            </li> -->
            <li class="nav-item {{ request()->is('reports/*') ? 'active' : '' }}" data-item="reports">
                <a class="nav-item-hold" href="">
                    <i class="nav-icon i-Receipt"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('indicators/*') ? 'active' : '' }}" data-item="indicators">
                <a class="nav-item-hold" href="">
                    <i class="nav-icon i-Receipt"></i>
                    <span class="nav-text">Indicators</span>
                </a>
                <div class="triangle"></div>
            </li>


        </ul>
    </div>

    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <!-- Submenu Dashboards -->
        <ul class="childNav" data-parent="dashboard">
            @if (Auth::user()->access_level == 'Facility')
            <li class="nav-item ">


                <a class="{{ Route::currentRouteName()=='dashboard' ? 'open' : '' }}" href="{{route('dashboard')}}">
                    <span class=" text-muted">Summary</span>
                </a>
                <a class="{{ Route::currentRouteName()=='Reports-facility_home' ? 'open' : '' }}" href="{{route('Reports-facility_home')}}">
                    <span class=" text-muted">Appointment Register</span>
                </a>
                <a class="{{ Route::currentRouteName()=='upi_dashboard' ? 'open' : '' }}" href="{{route('upi_dashboard')}}">
                    <span class=" text-muted">Client Verification</span>
                </a>
                <a class="{{ Route::currentRouteName()=='appointment-dashboard' ? 'open' : '' }}" href="{{route('appointment-dashboard')}}">
                    <span class=" text-muted">Appointment</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
            <li class="nav-item ">
                <a class="{{ Route::currentRouteName()=='dashboard' ? 'open' : '' }}" href="{{route('dashboard')}}">
                    <span class=" text-muted">Summary</span>
                </a>
                <a class="{{ Route::currentRouteName()=='upi_dashboard' ? 'open' : '' }}" href="{{route('upi_dashboard')}}">
                    <span class=" text-muted">Client Verification</span>
                </a>
                <a class="{{ Route::currentRouteName()=='appointment-dashboard' ? 'open' : '' }}" href="{{route('appointment-dashboard')}}">
                    <span class=" text-muted">Appointment</span>
                </a>
            </li>
            @endif

            </li>

            <!-- <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='report-appointment-dashboard' ? 'open' : '' }}" href="{{route('report-appointment-dashboard')}}">
                    <span class=" text-muted">Appointments</span>
                </a>
            </li> -->
            @if (Auth::user()->access_level == 'Admin')
            <!-- <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='report-IL-dashboard' ? 'open' : '' }}" href="{{route('report-IL-dashboard')}}">
                    <span class=" text-muted">IL Extract</span>
                </a>
            </li> -->
            @endif

            @if (Auth::user()->access_level == 'Admin')
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='sms-analytics' ? 'open' : '' }}" href="{{route('sms-analytics')}}">
                    <span class=" text-muted">SMS Analytics</span>
                </a>
            </li>
            @endif
        </ul>

        <ul class="childNav" data-parent="clients">

            @if (Auth::user()->access_level == 'Facility')
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='client_dashboard' ? 'open' : '' }}" href="{{route('client_dashboard')}}">
                    <span class="item-name">Clients Dashboard</span>
                </a>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Profile</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li><a href="{{route('profile')}}">
                            <span class="item-name">Client Profile</span>
                        </a>
                    </li>
                    <li><a href="{{route('hei-profile')}}">
                            <span class="item-name">HEI Profile</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='profile' ? 'open' : '' }}" href="{{route('profile')}}">
                    <span class="item-name">Client Profile</span>
                </a>
            </li> -->

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='consent-clients' ? 'open' : '' }}" href="{{route('consent-clients')}}">
                    <span class="item-name">Non Consented</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='clients_list' ? 'open' : '' }}" href="{{route('clients_list')}}">
                    <span class="item-name">Clients List</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='clients-extract' ? 'open' : '' }}" href="{{route('clients-extract')}}">
                    <span class="item-name">Client Extract</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='upload-clients-form' ? 'open' : '' }}" href={{route('upload-clients-form')}}>
                    <span class="item-name">Upload Clients</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='client_dashboard' ? 'open' : '' }}" href="{{route('client_dashboard')}}">
                    <span class="item-name">Clients Dashboard</span>
                </a>
            </li>
            @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')
            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='clients_list' ? 'open' : '' }}" href="{{route('clients_list')}}">
                    <span class="item-name">Clients List</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='upload-clients-form' ? 'open' : '' }}" href={{route('upload-clients-form')}}>
                    <span class="item-name">Upload Clients</span>
                </a>
            </li>
            @endif
            @endif
        </ul>
        @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
        <ul class="childNav" data-parent="main_appointments">
            <!-- <li class="nav-item">
                <a href="{{route('appointment_charts')}}">
                    <span class="item-name">Appointment Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('missed_appointment_charts')}}">
                    <span class="item-name">Missed Appointment Dashboard</span>
                </a>
            </li> -->
            <!-- <li class="nav-item">
                <a href="{{route('appointment_list')}}">
                    <span class="item-name">Appointment List</span>
                </a>
            </li> -->
        </ul>
        @endif
        @if (Auth::user()->access_level == 'Facility')
        <ul class="childNav" data-parent="appointments">
            <!-- <li class="nav-item">
                <a href="{{route('appointment_charts')}}">
                    <span class="item-name">Appointment Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('missed_appointment_charts')}}">
                    <span class="item-name">Missed Appointment Dashboard</span>
                </a>
            </li> -->
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Future Appointment Diary</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li><a href="{{route('get_future_appointments')}}">
                            <span class="item-name">Future</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="nav-item dropdown-sidemenu">
                <a>

                    <span class="item-name">Defaulter Diary</span>
                    <i class="dd-arrow i-Arrow-Down"></i>

                </a>
                <ul class="submenu">
                    <li>
                        <a href={{route('report-appointments-missed')}}>
                            <span class="item-name">Missed</span>
                        </a>
                    </li>
                    <li>
                        <a href={{route('report-appointments-defaulted')}}>
                            <span class="item-name">Defaulted</span>
                        </a>
                    </li>
                    <li>
                        <a href={{route('report-appointments-ltfu_clients')}}>
                            <span class="item-name">Lost To Follow Up</span>
                        </a>
                    </li>
                </ul>
            </li> -->
            <li class="nav-item">
                <a href="{{route('future-apps')}}">
                    <span class="item-name">Edit Appointment</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('appointment_list')}}">
                    <span class="item-name">Appointments List</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{route('app_calendar')}}">
                    <span class="item-name">Calender</span>
                </a>
            </li>

        </ul>

        <ul class="childNav" data-parent="wellness">
            <li class="nav-item">
                <a href={{route('report-ok_clients')}}>
                    <span class="item-name">Ok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href={{route('report-not_ok_clients')}}>
                    <span class="item-name">Not Ok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href={{route('report-unrecognised_response')}}>
                    <span class="item-name">Unrecognised</span>
                </a>
            </li>

        </ul>

        <ul class="childNav" data-parent="groups">
            <li class="nav-item">
                <a href={{route('report-adolescent_clients')}}>
                    <span class="item-name">Adolescent</span>
                </a>
            </li>

            <li class="nav-item">
                <a href={{route('report-pmtct_clients')}}>
                    <span class="item-name">PMTCT</span>
                </a>
            </li>
            <li class="nav-item">
                <a href={{route('report-adults_clients')}}>
                    <span class="item-name">Adult</span>
                </a>
            </li>

            <li class="nav-item">
                <a href={{route('report-paeds_clients')}}>
                    <span class="item-name">Paeds </span>
                </a>
            </li>


        </ul>
        @endif
        @if (Auth::user()->access_level == 'Facility')
        <ul class="childNav" data-parent="tracing">

            <li class="nav-item">
                <a href={{route('clients-booked')}}>
                    <span class="item-name">Clients Tracing</span>
                </a>
            </li>
            <li class="nav-item">
                <a href={{route('admin-tracer-clients')}}>
                    <span class="item-name">Tracing List</span>
                </a>
            </li>
        </ul>
        @endif
        <ul class="childNav" data-parent="admin">
            @if (Auth::user()->access_level == 'Admin')
            <li class="nav-item">
                <a class="" href="{{route('admin-donors')}}">
                    <span class="item-name">Donor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('admin-partners')}}">
                    <span class="item-name">Partner</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('admin-groups')}}">
                    <span class="item-name">Groups</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('admin_facilities')}}">
                    <span class="item-name">Facilities</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href={{route('admin-users')}}>
                    <span class="item-name">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="">
                    <span class="item-name">Content</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="" href="">
                    <span class="item-name">Roles</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="">
                    <span class="item-name">Language</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('broadcast-page')}}">
                    <span class="item-name">Broadcast</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Facility')
            <!-- <li class="nav-item">
                <a class="" href="{{route('clients-booked')}}">
                    <span class="item-name">Clients Tracing</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a class="" href="{{route('broadcast-page')}}">
                    <span class="item-name">Broadcast</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Partner')
            <li class="nav-item">
                <a class="" href={{route('admin-users')}}>
                    <span class="item-name">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('broadcast-page')}}">
                    <span class="item-name">Broadcast</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->access_level == 'Donor')
            <li class="nav-item">
                <a class="" href="{{route('admin-partners')}}">
                    <span class="item-name">Partner</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('admin_facilities')}}">
                    <span class="item-name">Facilities</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href={{route('admin-users')}}>
                    <span class="item-name">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="" href="{{route('broadcast-page')}}">
                    <span class="item-name">Broadcast</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County')
            <li class="nav-item">
                <a class="" href="{{route('admin-partners')}}">
                    <span class="item-name">Partner</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="" href={{route('admin-users')}}>
                    <span class="item-name">Users</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="" href="{{route('broadcast')}}">
                    <span class="item-name">Broadcast</span>
                </a>
            </li> -->
            @endif
        </ul>
        <!-- <ul class="childNav" data-parent="dsd">
            <li class="nav-item">
                <a class="" href="{{route('Reports-dsd')}}">
                    <span class="item-name">DSD Dairy</span>
                </a>
            </li>
        </ul> -->
        <!-- <ul class="childNav" data-parent="pmtct">
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">PMTCT Reports</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('report-pmtct-summary')}}">
                            <span class="item-name">Summary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-pmtct-appointment-dairy')}}">
                            <span class="item-name">App Diary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-pmtct-defaulter-dairy')}}">
                            <span class="item-name">Defaulter Diary</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">HEI Reports</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('report-hei-summary')}}">
                            <span class="item-name">Summary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-all_heis')}}">
                            <span class="item-name">HEI List</span>
                        </a>
                    </li>
                    <li>
                        <a class="" href="{{route('report-hei-appointment-dairy')}}">
                            <span class="item-name">App Diary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-hei-defaulter-dairy')}}">
                            <span class="item-name">Defaulter Diary</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-hei-final-outcome')}}">
                            <span class="item-name">Final Outcome</span>
                        </a>
                    </li>

                </ul>
            </li>

        </ul> -->

        <ul class="childNav" data-parent="reports">

            <!-- <li class="nav-item">
                <a class="" href="{{route('admin-tracer-clients')}}">
                    <span class="item-name">Client Tracer</span>
                </a>
            </li> -->

            @if (Auth::user()->access_level == 'Facility' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Client</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('report-consented')}}">
                            <span class="item-name">Consented</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('Reports-dsd')}}">
                            <span class="item-name">DSD Clients</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-pmtct_clients')}}">
                            <span class="item-name">PMTCT Clients</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-all_heis')}}">
                            <span class="item-name">HEI List</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin-tracer-clients')}}">
                            <span class="item-name">Client Tracer</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-transfer')}}">
                            <span class="item-name">Transfers</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('report-deactivated_clients')}}">
                            <span class="item-name">Deactivated</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Appointment</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('report-today_appointments')}}">
                            <span class="item-name">Today's Appointment</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('report-hei-appointment-dairy')}}">
                            <span class="item-name">HEI's Appointment</span>
                        </a>
                    </li>

                    @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
                    <li>
                        <a href="{{route('tracing-cost')}}">
                            <span class="item-name">Tracing Cost</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            <!-- <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">New PMTCT</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="">
                            <span class="item-name">Linelist</span>
                        </a>
                    </li>
                </ul>
            </li> -->
            @endif
            @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County' || Auth::user()->access_level == 'Facility')
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Messages</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('message_form')}}">
                            <span class="item-name">Client Messages</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown-sidemenu">
                <a>
                    <span class="item-name">Facilities</span>
                    <i class="dd-arrow i-Arrow-Down"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('my_facilities')}}">
                            <span class="item-name">My Facilities</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('active_facilities_list')}}">
                            <span class="item-name">Active Facilities</span>
                        </a>
                    </li>
                </ul>
            </li>

            @if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Facility')
            <li class="nav-item">
                <a href="{{route('report-lab_investigation')}}">
                    <span class="item-name">Lab Investigation</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->access_level == 'Admin')
            <li class="nav-item">
                <a href="{{route('access-report')}}">
                    <span class="item-name">User Report</span>
                </a>
            </li>
            @endif
            <!-- <li class="nav-item">
                <a href="{{route('monthly-appointment-summary')}}">
                    <span class="item-name">Monthly Appointment</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('client-summary-report')}}">
                    <span class="item-name">Summary Report</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a href="{{route('tracing-outcome-report')}}">
                    <span class="item-name">Tracing OutCome</span>
                </a>
            </li>

        </ul>
        <ul class="childNav" data-parent="indicators">
            <li class="nav-item">
                <a href={{route('indicators')}}>
                    <span class="item-name">Definitions</span>
                </a>
            </li>

        </ul>
        @endif
    </div>
    <div class="sidebar-overlay"></div>
</div>
<!--=============== Left side End ================-->