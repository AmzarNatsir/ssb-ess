    <!-- Search Modal -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-transparent">
                <div class="card shadow-none mb-0">
                    <div class="px-3 py-2 d-flex flex-row align-items-center" id="search-top">
                        <i class="ti ti-search fs-22"></i>
                        <input type="search" class="form-control border-0" placeholder="Search">
                        <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x fs-22"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidenav Menu Start -->
    <div class="sidebar" id="sidebar">

        <!-- Start Logo -->
        <div class="sidebar-logo">
            <div>
                <!-- Logo Normal -->
                <a href="{{url('index')}}" class="logo logo-normal">
                    <img src="{{URL::asset('build/img/logo.svg')}}" alt="Logo">
                </a>

                <!-- Logo Small -->
                <a href="{{url('index')}}" class="logo-small">
                    <img src="{{URL::asset('build/img/logo-small.svg')}}" alt="Logo">
                </a>

                <!-- Logo Dark -->
                <a href="{{url('index')}}" class="dark-logo">
                    <img src="{{URL::asset('build/img/logo-white.svg')}}" alt="Logo">
                </a>
            </div>
            <button class="sidenav-toggle-btn btn border-0 p-0 active" id="toggle_btn">
                <i class="ti ti-arrow-bar-to-left"></i>
            </button>

            <!-- Sidebar Menu Close -->
            <button class="sidebar-close">
                <i class="ti ti-x align-middle"></i>
            </button>
        </div>
        <!-- End Logo -->

        <!-- Sidenav Menu -->
        <div class="sidebar-inner" data-simplebar>
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title"><span>SELF SERVICE</span></li>
                    <li>
                        <ul>
                            <li class=" {{ Request::is('my-profile') ? 'active' : '' }}">
                                <a href="{{ route('my-profile') }}" ><i class="ti ti-user-up"></i><span>My Profile</span></a>
                            </li>
                            <li class="{{ Request::is('leave') ? 'active' : '' }}">
                                <a href="{{ route('leave.index') }}"><i class="ti ti-building-community"></i><span>Leave</span></a>
                            </li>
                            <li class="{{ Request::is('permission') ? 'active' : '' }}">
                                <a href="{{ route('permission.index') }}"><i class="ti ti-medal"></i><span>Permission</span></a>
                            </li>
                            <li class="{{ Request::is('overtime', 'overtime/*') ? 'active' : '' }}">
                                <a href="{{ route('overtime.index') }}"><i class="ti ti-clock-share"></i><span>Overtime</span></a>
                            </li>
                            <li class="{{ Request::is('attendence*') ? 'active' : '' }}">
                                <a href="{{ route('attendence.index') }}"><i class="ti ti-calendar-check"></i><span>Attendance</span></a>
                            </li>
                            <li class="{{ Request::is('perdis*') ? 'active' : '' }}">
                                <a href="{{ route('perdis.index') }}"><i class="ti ti-brand-campaignmonitor"></i><span>Official travel</span></a>
                            </li>
                            <li class="{{ Request::is('memorandum*') ? 'active' : '' }}">
                                <a href="{{ route('memorandum.index') }}"><i class="ti ti-atom-2"></i><span>Memorandum</span></a>
                            </li>
                            <li class="{{ Request::is('tasks', 'tasks-completed', 'tasks-important') ? 'active' : '' }}">
                                <a href="{{url('tasks')}}"><i class="ti ti-list-check"></i><span>Training</span></a>
                            </li>
                            <li class="{{ Request::is('proposals', 'proposals-list', 'proposal-details') ? 'active' : '' }}">
                                <a href="{{url('proposals')}}"><i class="ti ti-file-star"></i><span>Employee loans</span></a>
                            </li>
                            <li class="{{ Request::is('contracts', 'contracts-list', 'contract-details') ? 'active' : '' }}">
                                <a href="{{url('contracts')}}"><i class="ti ti-file-check"></i><span>Payroll</span></a>
                            </li>
                            <li class="{{ Request::is('estimations', 'estimations-list', 'estimation-details') ? 'active' : '' }}">
                                <a href="{{url('estimations')}}"><i class="ti ti-file-report"></i><span>Resign</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-title"><span>Help</span></li>
                </ul>
            </div>
        </div>

    </div>
    <!-- Sidenav Menu End -->
