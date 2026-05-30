<!-- Topbar Start -->
<header class="navbar-header">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Logo -->
            <a href="{{url('index')}}" class="logo">

                <!-- Logo Normal -->
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{URL::asset('build/img/logo.svg')}}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{URL::asset('build/img/logo-small.svg')}}" alt="small logo"></span>
                </span>

                <!-- Logo Dark -->
                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{URL::asset('build/img/logo-white.svg')}}" alt="dark logo"></span>
                </span>
            </a>

            <!-- Sidebar Mobile Button -->
            <a id="mobile_btn" class="mobile-btn" href="#sidebar">
                <i class="ti ti-menu-deep fs-24"></i>
            </a>

            @if (!Route::is(['layout-hidden']))
                <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn2">
                    <i class="ti ti-arrow-bar-to-right"></i>
                </button>
            @endif

            @if (Route::is(['layout-hidden']))
                <button class="sidenav-toggle-btn btn border-0 p-0" id="toggle_btn">
                    <i class="ti ti-arrow-bar-to-right"></i>
                </button>
            @endif

            <!-- Search -->
            <div class="me-auto d-flex align-items-center header-search d-lg-flex d-none">
                <!-- Search -->
                <div class="input-icon position-relative me-2">
                    <input type="text" class="form-control" placeholder="Search Keyword">
                    <span class="input-icon-addon d-inline-flex p-0 header-search-icon"><i
                            class="ti ti-command"></i></span>
                </div>
                <!-- /Search -->
            </div>

        </div>

        <div class="d-flex align-items-center">

            <!-- Search for Mobile -->
            <div class="header-item d-flex d-lg-none me-2">
                <button class="topbar-link btn" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                    <i class="ti ti-search fs-16"></i>
                </button>
            </div>


            <!-- Minimize -->
            <div class="header-item">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="btn topbar-link btnFullscreen"><i
                            class="ti ti-maximize"></i></a>
                </div>
            </div>
            <!-- Minimize -->

            @if (!Route::is(['layout-mini', 'layout-hoverview', 'layout-hidden', 'layout-fullwidth', 'layout-rtl', 'layout-dark']))
                <!-- Light/Dark Mode Button -->
                <div class="header-item d-none d-sm-flex me-2">
                    <button class="topbar-link btn topbar-link" id="light-dark-mode" type="button">
                        <i class="ti ti-moon fs-16"></i>
                    </button>
                </div>
            @endif

            <!-- pages -->
            <div class="header-line"></div>
            <!-- Notification Dropdown -->
            <div class="header-item">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="btn topbar-link position-relative" data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-bell fs-16"></i>
                        @if(($progressNotifications['unreadCount'] ?? 0) > 0)
                            <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size: 10px; min-width: 18px;">
                                {{ $progressNotifications['unreadCount'] > 99 ? '99+' : $progressNotifications['unreadCount'] }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg p-0" style="min-width: 360px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">Notifikasi Progress Pengajuan</h6>
                            <form action="{{ route('notifications.progress.mark-all-read') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light" {{ ($progressNotifications['unreadCount'] ?? 0) === 0 ? 'disabled' : '' }}>
                                    Mark all as read
                                </button>
                            </form>
                        </div>

                        <div style="max-height: 360px; overflow-y: auto;">
                            @forelse(($progressNotifications['items'] ?? collect()) as $notif)
                                <a href="{{ $notif['read_url'] }}" class="dropdown-item py-3 px-3 border-bottom bg-light">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark">{{ $notif['title'] }}</div>
                                            <div class="small text-muted">Status: <span class="fw-medium">{{ $notif['status_label'] }}</span></div>
                                            <div class="small text-muted mt-1">{{ $notif['updated_at_human'] }}</div>
                                        </div>
                                        <span class="badge bg-light-warning text-warning">Baru</span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted small">
                                    Semua notifikasi sudah terbaca.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown profile-dropdown d-flex align-items-center justify-content-center">
                <a href="javascript:void(0);" class="topbar-link dropdown-toggle drop-arrow-none position-relative"
                    data-bs-toggle="dropdown" data-bs-offset="0,22" aria-haspopup="false" aria-expanded="false">
                    @if(Auth::user()->karyawan->photo)
                        <img src="{{ Auth::user()->karyawan->photo_url }}" width="38" height="38" class="rounded-1 d-flex" alt="user-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    @endif
                    <span class="avatar avatar-sm avatar-rounded bg-primary text-white {{ Auth::user()->karyawan->photo ? '' : 'd-flex' }}" style="{{ Auth::user()->karyawan->photo ? 'display:none;' : '' }}">
                        {{ Auth::user()->karyawan->initials }}
                    </span>
                    <span class="online text-success"><i
                            class="ti ti-circle-filled d-flex bg-white rounded-circle border border-1 border-white"></i></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-2">

                    <div class="d-flex align-items-center bg-light rounded-3 p-2 mb-2">
                        @if(Auth::user()->karyawan->photo)
                            <img src="{{ Auth::user()->karyawan->photo_url }}" class="rounded-circle" width="42" height="42" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <span class="avatar avatar-md avatar-rounded bg-primary text-white {{ Auth::user()->karyawan->photo ? '' : 'd-flex' }}" style="{{ Auth::user()->karyawan->photo ? 'display:none;' : '' }}">
                            {{ Auth::user()->karyawan->initials }}
                        </span>
                        <div class="ms-2">
                            <p class="fw-medium text-dark mb-0 text-capitalize">
                                {{ Auth::user()->karyawan->nm_lengkap ?? Auth::user()->nik }}
                            </p>
                            <span
                                class="d-block fs-13">{{ Auth::user()->karyawan->jabatan->nm_jabatan ?? 'Developer' }}</span>
                        </div>
                    </div>

                    <!-- Item-->
                    <a href="{{ route('my-profile') }}" class="dropdown-item">
                        <i class="ti ti-user me-1 align-middle"></i>
                        <span class="align-middle">My Profile</span>
                    </a>

                    <!-- Item-->
                    <div class="pt-2 mt-2 border-top">
                        <a href="javascript:void(0);" class="dropdown-item text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Sign Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
<!-- Topbar End -->
