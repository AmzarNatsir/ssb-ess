@php
    use App\Helpers\HrdConstants;
@endphp

@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">My Profile</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>                
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <!-- Profile Card -->
                    <div class="card overflow-hidden">
                        <div class="card-body p-0">
                            <div class="bg-primary pt-5 px-3 pb-3 text-center position-relative">
                                <div class="avatar avatar-xxl avatar-rounded border border-4 border-white mx-auto mb-2 shadow-sm bg-white overflow-hidden">
                                    <img src="{{URL::asset('build/img/users/user-40.jpg')}}" alt="user">
                                </div>
                                <h5 class="text-white mb-1 text-capitalize">{{ $karyawan->nm_lengkap ?? $user->nik }}</h5>
                                <p class="text-white-50 mb-0 fs-13">{{ $karyawan->jabatan->nm_jabatan ?? 'Employee' }}</p>
                            </div>
                            <div class="p-3">
                                <ul class="list-group list-group-flush border-0">
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">NIK</span>
                                        <span class="fw-medium text-dark">{{ $user->nik }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">Department</span>
                                        <span class="fw-medium text-dark">{{ $karyawan->departemen->nm_dept ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">Join Date</span>
                                        <span class="fw-medium text-dark">{{ $karyawan->tgl_masuk ? date('d M Y', strtotime($karyawan->tgl_masuk)) : '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Contact Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-light-info text-info rounded flex-shrink-0">
                                    <i class="ti ti-mail fs-20"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-0 fs-12">Email Address</p>
                                    <h6 class="mb-0 fs-14">{{ $karyawan->nmemail ?? '-' }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md bg-light-success text-success rounded flex-shrink-0">
                                    <i class="ti ti-phone fs-20"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-0 fs-12">Phone Number</p>
                                    <h6 class="mb-0 fs-14">{{ $karyawan->notelp ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <!-- Personal Details -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0">Personal Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-gap-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Full Name</p>
                                    <h6 class="text-dark text-capitalize">{{ $karyawan->nm_lengkap ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Gender</p>
                                    <h6 class="text-dark">{{ $karyawan->jenkel == 1 ? 'Male' : ($karyawan->jenkel == 2 ? 'Female' : '-') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Place of Birth</p>
                                    <h6 class="text-dark">{{ $karyawan->tmp_lahir ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Date of Birth</p>
                                    <h6 class="text-dark">{{ $karyawan->tgl_lahir ? date('d F Y', strtotime($karyawan->tgl_lahir)) : '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Identity Number (KTP)</p>
                                    <h6 class="text-dark">{{ $karyawan->no_ktp ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Religion</p>
                                    <h6 class="text-dark">{{ HrdConstants::AGAMA[$karyawan->agama] ?? '-' }}</h6>
                                </div>
                                <div class="col-md-12">
                                    <p class="text-muted mb-1 fs-12">Current Address</p>
                                    <h6 class="text-dark">{{ $karyawan->alamat ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Employment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-gap-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Department</p>
                                    <h6 class="text-dark">{{ $karyawan->departemen->nm_dept ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Sub Department</p>
                                    <h6 class="text-dark">{{ $karyawan->subDepartemen->nm_subdept ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Division</p>
                                    <h6 class="text-dark">{{ $karyawan->divisi->nm_divisi ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Length of Service</p>
                                    <h6 class="text-dark">{{ $karyawan->lama_bekerja }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Employee Status</p>
                                    <span class="badge bg-light-success text-success">{{ HrdConstants::STATUS_KARYAWAN[$karyawan->id_status_karyawan] ?? 'Active' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Last Education</p>
                                    <h6 class="text-dark">{{ HrdConstants::JENJANG_PENDIDIKAN[$karyawan->pendidikan_akhir] ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Marital Status</p>
                                    <h6 class="text-dark">{{ HrdConstants::STATUS_PERNIKAHAN[$karyawan->status_nikah] ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
