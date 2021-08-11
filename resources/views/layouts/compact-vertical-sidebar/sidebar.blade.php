<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">
            <li class="nav-item {{ request()->is('dashboard/*') ? 'active' : '' }} {{ request()->is('large-compact-sidebar/dashboard/*') ? 'active' : '' }}"
                data-item="dashboard">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('clients/*') ? 'active' : '' }}" data-item="clients">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Library"></i>
                    <span class="nav-text">Clients</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('appointments/*') ? 'active' : '' }}" data-item="appointments">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Appointments</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('wellness/*') ? 'active' : '' }}" data-item="wellness">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Wellness</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('groups/*') ? 'active' : '' }}" data-item="groups">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Groups</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('tracing/*') ? 'active' : '' }}" data-item="tracing">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Groups</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('admin/*') ? 'active' : '' }}" data-item="admin">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Computer-Secure"></i>
                    <span class="nav-text">Administration Tool</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('reports/*') ? 'active' : '' }}" data-item="reports">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-File-Clipboard-File--Text"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <div class="triangle"></div>
            </li>

            <li class="nav-item {{ request()->is('logout/*') ? 'active' : '' }}" data-item="logout">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Bar-Chart-5"></i>
                    <span class="nav-text">Log Out</span>
                </a>
                <div class="triangle"></div>
            </li

        </ul>
    </div>

    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <i class="sidebar-close i-Close" (click)="toggelSidebar()"></i>
        <header>
            <div class="logo">
                <img src="{{ asset('assets/images/ushauri.jpeg') }}" alt="">
            </div>
        </header>
        <!-- Submenu Dashboards -->
        <div class="submenu-area" data-parent="dashboard">
            <header>
                <h6>Dashboards</h6>
            </header>
            <ul class="childNav" data-parent="dashboard">
            @if (Auth::user()->access_level == 'Facility')
                <li class="nav-item ">

                                <a class="{{ Route::currentRouteName()=='Reports-facility_home' ? 'open' : '' }}" href="{{route('Reports-facility_home')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>
                                @endif
                                @if (Auth::user()->access_level == 'Admin')
                                <a class="{{ Route::currentRouteName()=='Reports-dashboard' ? 'open' : '' }}" href="{{route('Reports-dashboard')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>
                                @endif
                                @if (Auth::user()->access_level == 'Partner')
                                <a class="{{ Route::currentRouteName()=='Reports-dashboard' ? 'open' : '' }}" href="{{route('Reports-dashboard')}}">
                                    <span class=" text-muted">Summary</span>
                                </a>

                </li>
                @endif
                <li class="nav-item">
                     <a class="{{ Route::currentRouteName()=='report-appointment-dashboard' ? 'open' : '' }}" href="{{route('report-appointment-dashboard')}}">
                            <span class=" text-muted">Appointments</span>
                        </a>
                </li>
                @if (Auth::user()->access_level == 'Admin')
                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='report-IL-dashboard' ? 'open' : '' }}" href="{{route('report-IL-dashboard')}}">
                                    <span class=" text-muted">IL Extract</span>
                                </a>
                </li>
                @endif

            </ul>
        </div>
        <div class="submenu-area" data-parent="clients">
            <header>
                <h6>Clients</h6>
            </header>
            <ul class="childNav" data-parent="clients">
            @if (Auth::user()->access_level == 'Facility')

                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='profile' ? 'open' : '' }}" href="{{route('profile')}}">
                                    <span class="item-name">Client Profile</span>
                                </a>
                </li>

                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='consent-clients' ? 'open' : '' }}" href="{{route('consent-clients')}}">
                                    <span class="item-name">Non Consented</span>
                                </a>
                </li>
                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='report-clients-list' ? 'open' : '' }}" href={{route('report-clients-list')}}>
                                    <span class="item-name">Clients</span>
                                </a>
                </li>

                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='clients-extract' ? 'open' : '' }}" href="{{route('clients-extract')}}">
                            <span class="item-name">Client Extract</span>
                        </a>
                </li>
                @endif
                @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')

                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='Reports-clients_dashboard' ? 'open' : '' }}" href="{{route('Reports-clients_dashboard')}}">
                                    <span class="item-name">Clients</span>
                                </a>
                </li>
                <li class="nav-item">
                <a class="{{ Route::currentRouteName()=='upload-clients-form' ? 'open' : '' }}" href={{route('upload-clients-form')}}>
                            <span class="item-name">Upload Clients</span>
                        </a>
                </li>
                @endif

            </ul>
        </div>
        @if (Auth::user()->access_level == 'Facility')
        <div class="submenu-area" data-parent="appointments">
            <header>
                <h6>Appointments</h6>
            </header>
            <ul class="childNav" data-parent="appointments">
            <li class="nav-item dropdown-sidemenu">
                    <a>
                        <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                        <span class="item-name">Appointment Diary</span>
                        <i class="dd-arrow i-Arrow-Down"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{route('get_future_appointments')}}">
                                        <span class="item-name">Future</span>
                                    </a>
                            </li>
                    </ul>
                </li>
                <li class="nav-item dropdown-sidemenu">
                    <a>
                        <i class="nav-icon i-File-Clipboard-Text--Image"></i>
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
                </li>
                <li class="nav-item">
                  <a href="{{route('future-apps')}}">
                    <span class="item-name">Edit Appointment</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('report-appointments')}}">
                    <span class="item-name">Appointments</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#">
                    <span class="item-name">Calender</span>
                  </a>
                </li>

            </ul>
        </div>

        <div class="submenu-area" data-parent="wellness">
            <header>
                <h6>Wellness</h6>
            </header>
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
        </div>
        <div class="submenu-area" data-parent="groups">
            <header>
                <h6>Groups</h6>
            </header>
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
        </div>
        @endif
        @if (Auth::user()->access_level == 'Facility')
        <div class="submenu-area" data-parent="tracing">
            <header>
                <h6>Tracing</h6>
            </header>
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
        </div>
        @endif
        <div class="submenu-area" data-parent="admin">
            <header>
                <h6>Administration Tools</h6>
            </header>
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
                    <span class="item-name">Module Mngr</span>
                </a>
                </li>
                <li class="nav-item">
                <a class="" href="">
                     <span class="item-name">Sender</span>
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
                <a class="" href="">
                     <span class="item-name">Notification Conf</span>
                 </a>
                </li>
                <li class="nav-item">
                <a class="" href="">
                     <span class="item-name">Role Modules</span>
                 </a>
                </li>
                <li class="nav-item">
                <a class="" href="{{route('broadcast')}}">
                 <span class="item-name">Broadcast</span>
                </a>
                </li>
                @endif
                @if (Auth::user()->access_level == 'Facility')
                <li class="nav-item">
                <a class="" href="{{route('clients-booked')}}">
                    <span class="item-name">Clients Tracing</span>
                </a>
                </li>
                <li class="nav-item">
                <a class="" href="{{route('broadcast')}}">
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
                 <a class="" href="{{route('broadcast')}}">
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
                <a class="" href="{{route('broadcast')}}">
                    <span class="item-name">Broadcast</span>
                 </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="submenu-area" data-parent="reports">
            <header>
                <h6>Reports</h6>
            </header>
            <ul class="childNav" data-parent="reports">
            @if (Auth::user()->role_id == 12 || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Admin')
                <li class="nav-item">
                 <a class="" href="{{route('admin-tracer-clients')}}">
                    <span class="item-name">Client Tracer</span>
                 </a>
                </li>
            @endif
               <li class="nav-item dropdown-sidemenu">
                    <a>
                        <i class="nav-icon i-File-Clipboard-Text--Image"></i>
                        <span class="item-name">DSD Reports</span>
                        <i class="dd-arrow i-Arrow-Down"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a class="" href="{{route('Reports-dsd')}}">
                                <span class="item-name">DSD Dairy</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown-sidemenu">
                    <a>
                        <i class="nav-icon i-File-Clipboard-Text--Image"></i>
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
                        <i class="nav-icon i-File-Clipboard-Text--Image"></i>
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
                @if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor')
                <li class="nav-item">
                  <a class="" href="{{route('broadcast')}}">
                    <span class="item-name">Broadcast</span>
                  </a>
                </li>
                <li class="nav-item">
                   <a class="" href="{{route('tracing-cost')}}">
                     <span class="item-name">Tracing Cost</span>
                   </a>
                </li>
                @endif
                <li class="nav-item">
                  <a href="{{route('report-lab_investigation')}}">
                    <span class="item-name">Lab Investigation</span>
                  </a>
                </li>
                <li class="nav-item">
                   <a class="" href="{{route('my_facilities')}}">
                        <span class="item-name">My Facilities</span>
                    </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('report-deactivated_clients')}}">
                    <span class="item-name">Deactivated</span>
                  </a>
                </li>
                <li class="nav-item">
                 <a href="{{route('report-transfer')}}">
                    <span class="item-name">Transfers</span>
                 </a>
                </li>
                <li class="nav-item">
                 <a href="{{route('message-extract-report')}}">
                    <span class="item-name">Messages Extract</span>
                 </a>
                </li>
                @if (Auth::user()->access_level == 'Admin')
                <li class="nav-item">
                  <a href="{{route('access-report')}}">
                    <span class="item-name">User Report</span>
                  </a>
                </li>
                @endif
                <li class="nav-item">
                  <a href="{{route('report-consented')}}">
                    <span class="item-name">Consented Reports</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('report-today_appointments')}}">
                    <span class="item-name">Todays Appointment</span>
                 </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('monthly-appointment-summary')}}">
                    <span class="item-name">Monthly Appointment</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('client-summary-report')}}">
                    <span class="item-name">Summary Report</span>
                 </a>
                </li>
                <li class="nav-item">
                   <a href="{{route('tracing-outcome-report')}}">
                        <span class="item-name">Tracing OutCome</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="submenu-area" data-parent="logout">
            <header>
                <h6>Pages</h6>

            </header>
            <ul class="childNav" data-parent="logout">
            <li class="nav-item">
                   <a href="{{route('logout')}}">
                        <span class="item-name">Log Out</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="sidebar-overlay"></div>
</div>
<!--=============== Left side End ================-->