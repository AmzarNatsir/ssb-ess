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
                        <div class="vh-100 d-flex justify-content-between flex-column p-4 pb-0">
                            <div class="text-center mb-4 auth-logo">
                                <img src="{{URL::asset('assets/logo_perusahaan/logo_ssb.png')}}" class="img-fluid"
                                    alt="Logo">
                            </div>
                            <div>
                                <div class="mb-4">
                                    <h3 class="mb-2">Sign In</h3>
                                    <p class="mb-0">Masuk menggunakan akun SSB Anda (Single Sign-On).</p>
                                </div>
                                @if (request('logout') || session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status', 'Anda telah keluar dari aplikasi.') }}
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <a href="{{ route('ssb.redirect') }}" class="btn btn-success w-100">
                                        <i class="ti ti-login me-1"></i> Masuk dengan SSO
                                    </a>
                                </div>
                            </div>
                            <div class="text-center pb-4">
                                <p class="text-dark mb-0">Copyright &copy;
                                    <script>document.write(new Date().getFullYear())</script> - SSB 2.0
                                </p>
                            </div>
                        </div>
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
