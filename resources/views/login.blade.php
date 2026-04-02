<?php $page = 'login'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="overflow-hidden p-3 acc-vh">

        <!-- start row -->
        <div class="row vh-100 w-100 g-0">

            <div class="col-lg-6 vh-100 overflow-y-auto overflow-x-hidden">

                <!-- start row -->
                <div class="row">

                    <div class="col-md-10 mx-auto">
                        <form action="{{ route('login') }}" method="POST" class=" vh-100 d-flex justify-content-between flex-column p-4 pb-0">
                            @csrf
                            <div class="text-center mb-4 auth-logo">
                                <img src="{{URL::asset('assets/logo_perusahaan/logo_ssb.png')}}" class="img-fluid" alt="Logo">
                            </div>
                            <div>
                                <div class="mb-3">
                                    <h3 class="mb-2">Sign In</h3>
                                    <p class="mb-0">Access using your NIK and passcode.</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <div class="input-group input-group-flat">
                                        <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" required autofocus>
                                        <span class="input-group-text">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                        @error('nik')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group input-group-flat pass-group">
                                        <input type="password" name="password" class="form-control pass-input @error('password') is-invalid @enderror" required>
                                        <span class="input-group-text toggle-password ">
                                            <i class="ti ti-eye-off"></i>
                                        </span>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                </div>
                            </div>
                            <div class="text-center pb-4">
                                <p class="text-dark mb-0">Copyright &copy; <script>document.write(new Date().getFullYear())</script> - SSB 2.0</p>
                            </div>
                        </form>
                    </div> <!-- end col -->

                </div>
                <!-- end row -->

            </div>

            <div class="col-lg-6 account-bg-01"></div> <!-- end col -->

        </div>
        <!-- end row -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
