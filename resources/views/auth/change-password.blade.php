<?php $page = 'password-change'; ?>
@extends('layout.mainlayout')
@section('content')

    <div class="account-content">
        <div class="container">
            <div class="account-logo text-center mt-5">
                <img src="{{ URL::asset('assets/logo_perusahaan/logo_ssb.png') }}" style="width: 200px" alt="Logo">
            </div>
            <div class="row display-flex justify-content-center">
                <div class="col-md-6">
                    <div class="account-box py-4 px-2 mt-4 login-content bg-white">
                        <div class="account-wrapper">
                            <h3 class="account-title fw-bold">Change Password</h3>
                            <p class="account-subtitle text-muted mb-6">Must be at least 8 characters, including a
                                combination of uppercase letters, numbers, and special characters.</p>

                            <form action="{{ route('password.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group input-group-flat pass-group">
                                        <input type="password" name="password"
                                            class="form-control pass-input @error('password') is-invalid @enderror"
                                            placeholder="Enter New Password" required minlength="8">
                                        <span class="input-group-text toggle-password">
                                            <i class="ti ti-eye-off"></i>
                                        </span>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group input-group-flat pass-group">
                                        <input type="password" name="password_confirmation" class="form-control pass-input"
                                            placeholder="Confirm New Password" required minlength="8">
                                        <span class="input-group-text toggle-password">
                                            <i class="ti ti-eye-off"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary w-100" type="submit">Change Password</button>
                                </div>
                                <div class="account-footer text-center">
                                    <p>Don't want to change now? <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                    </p>
                                </div>
                            </form>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection