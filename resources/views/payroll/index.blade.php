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
                    <h4 class="mb-1">Payroll</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Payroll</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="card shadow-sm border">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">Payroll History - {{ $currentYear }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Period</th>
                                    <th>Total Pay (THP)</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payrolls as $payroll)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-md bg-primary-transparent text-primary rounded me-3">
                                                    <i class="ti ti-receipt fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">
                                                        {{ HrdConstants::MONTHS[(int) $payroll->bulan] ?? 'Unknown' }}
                                                        {{ $payroll->tahun }}
                                                    </h6>
                                                    <small class="text-muted">Salary Period</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">Rp
                                                {{ number_format($payroll->thp, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('payroll.show', $payroll->id) }}"
                                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                    <i class="ti ti-eye me-1"></i> Detail
                                                </a>
                                                <a href="{{ route('payroll.print', $payroll->id) }}" target="_blank"
                                                    class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                    <i class="ti ti-printer me-1"></i> Cetak Slip
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div
                                                class="avatar avatar-xl bg-light-secondary text-secondary rounded-circle mx-auto mb-3">
                                                <i class="ti ti-file-off fs-1"></i>
                                            </div>
                                            <h5 class="text-muted mb-1">No Payroll Records Found</h5>
                                            <p class="text-muted small mb-0">No approved payroll records available for the year
                                                {{ $currentYear }}.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection