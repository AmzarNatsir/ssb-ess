@php
    use App\Helpers\HrdConstants;
@endphp

<?php $page = 'profile-setings'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Profile</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh" data-bs-original-title="Refresh"><i class="ti ti-refresh"></i></a>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Collapse" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                </div>
            </div>                
            <!-- End Page Header -->

            @component('components.settings-menu')
            @endcomponent

            <!-- start row -->
            <div class="row">

                @component('components.settings-sidebar')
                @endcomponent

                <div class="col-xl-9 col-lg-12">

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5 class="mb-0 fs-17">Profile</h5>
                            </div>
                            <form action="{{url('profile-settings')}}">
                                <div class="mb-3">
                                    <h6 class="mb-1">Employee Information</h6>
                                    <p class="mb-0">Provide the information below</p>
                                </div>
                                <div class="mb-3">
                                    <div class="profile-upload d-flex align-items-center">
                                        <div class="profile-upload-img avatar avatar-xxl border border-dashed rounded position-relative flex-shrink-0">
                                            <span><i class="ti ti-photo"></i></span>
                                            <img id="ImgPreview" src="{{URL::asset('build/img/profiles/avatar-02.jpg')}}" alt="img" class="preview1">
                                            <a href="javascript:void(0);" id="removeImage1" class="profile-remove">
                                                <i class="ti ti-x"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom mb-3">
                                    
                                    <!-- start row -->
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Full Name <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="nm_lengkap" value="{{ $karyawan->nm_lengkap ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    NIK <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="nik" value="{{ $user->nik ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Position <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" value="{{ $karyawan->jabatan->nm_jabatan ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Phone Number <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="notelp" value="{{ $karyawan->notelp ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="nmemail" value="{{ $karyawan->nmemail ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Religion</label>
                                                <input type="text" class="form-control" value="{{ HrdConstants::AGAMA[$karyawan->agama] ?? '-' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Education</label>
                                                <input type="text" class="form-control" value="{{ HrdConstants::JENJANG_PENDIDIKAN[$karyawan->pendidikan_akhir] ?? '-' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Marital Status</label>
                                                <input type="text" class="form-control" value="{{ HrdConstants::STATUS_PERNIKAHAN[$karyawan->status_nikah] ?? '-' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                    </div>
                                    <!-- end row -->

                                </div>
                                <div class="border-bottom mb-3">
                                    <div class="mb-3">
                                        <h6 class="mb-1">Address & Birth</h6>
                                        <p class="mb-0">Personal address and birth details</p>
                                    </div>

                                    <!-- start row -->
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Address
                                                </label>
                                                <input type="text" class="form-control" name="alamat" value="{{ $karyawan->alamat ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-6 col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Place of Birth
                                                </label>
                                                <input type="text" class="form-control" value="{{ $karyawan->tmp_lahir ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-lg-6 col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Date of Birth
                                                </label>
                                                <input type="text" class="form-control" value="{{ $karyawan->tgl_lahir ?? '' }}" readonly>
                                            </div>
                                        </div> <!-- end col -->

                                    </div>
                                    <!-- end row -->

                                </div>
                                <div class="d-flex align-items-center justify-content-end flex-wrap gap-2">
                                    <a href="#" class="btn btn-sm btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->

                </div> <!-- end col -->              

            </div>
			<!-- end row -->

        </div>
        <!-- End Content -->   

        @component('components.footer')
        @endcomponent

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection   