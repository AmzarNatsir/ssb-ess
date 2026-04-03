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
                    <h4 class="mb-1">Detail Payroll</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Payroll</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ HrdConstants::MONTHS[(int) $payroll->bulan] }} {{ $payroll->tahun }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('payroll.print', $payroll->id) }}" target="_blank"
                        class="btn btn-primary d-inline-flex align-items-center">
                        <i class="ti ti-printer me-1"></i> Cetak Slip
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm border mb-4">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div
                                    class="avatar avatar-xxl bg-primary-transparent text-primary rounded-circle mx-auto mb-3">
                                    <i class="ti ti-currency-dollar fs-1"></i>
                                </div>
                                <h4 class="fw-bold mb-1">Rp {{ number_format($payroll->thp, 0, ',', '.') }}</h4>
                                <p class="text-muted">Take Home Pay</p>
                            </div>
                            <hr>
                            <div class="mb-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Period:</span>
                                    <span class="fw-medium">{{ HrdConstants::MONTHS[(int) $payroll->bulan] }}
                                        {{ $payroll->tahun }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">NIK:</span>
                                    <span class="fw-medium">{{ $karyawan->nik }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Position:</span>
                                    <span class="fw-medium">{{ $karyawan->jabatan->nm_jabatan ?? '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Department:</span>
                                    <span class="fw-medium">{{ $karyawan->departemen->nm_dept ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-semibold">Breakdown Gaji</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- Penerimaan -->
                                <div class="col-md-6 border-end">
                                    <div class="p-4">
                                        <h6 class="text-primary fw-bold mb-3"><i
                                                class="ti ti-trending-up me-2"></i>Penerimaan (Earnings)</h6>
                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Gaji Pokok</span>
                                                <span class="fw-medium">Rp
                                                    {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Tunjangan Perusahaan</span>
                                                <span class="fw-medium">Rp
                                                    {{ number_format($payroll->tunj_perusahaan, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1 border-bottom-dashed">
                                                <span>Hours Meter</span>
                                                <span class="fw-medium">Rp
                                                    {{ number_format($payroll->hours_meter, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Lembur</span>
                                                <span class="fw-medium">Rp
                                                    {{ number_format($payroll->lembur, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Bonus</span>
                                                <span class="fw-medium">Rp
                                                    {{ number_format($payroll->bonus, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                                <span class="fw-bold">Total Penerimaan (Bruto)</span>
                                                <span class="fw-bold">Rp
                                                    {{ number_format($payroll->gaji_bruto, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Potongan -->
                                <div class="col-md-6">
                                    <div class="p-4">
                                        <h6 class="text-danger fw-bold mb-3"><i
                                                class="ti ti-trending-down me-2"></i>Potongan (Deductions)</h6>
                                        <div class="mb-0">
                                            @php
                                                $totalBpjsKaryawan = $payroll->bpjsks_karyawan + $payroll->bpjstk_jht_karyawan + $payroll->bpjstk_jp_karyawan;
                                                $totalPotonganLain = $payroll->pot_sedekah + $payroll->pot_pkk + $payroll->pot_air + $payroll->pot_rumah + $payroll->pot_toko_alif;
                                                $totalPotongan = $totalBpjsKaryawan + $totalPotonganLain;
                                            @endphp
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>BPJS</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($totalBpjsKaryawan, 0, ',', '.') }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Sedekah Bulanan</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($payroll->pot_sedekah, 0, ',', '.') }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>PKK</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($payroll->pot_pkk, 0, ',', '.') }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Air</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($payroll->pot_air, 0, ',', '.') }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Rumah</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($payroll->pot_rumah, 0, ',', '.') }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 py-1">
                                                <span>Toko Alif</span>
                                                <span class="fw-medium text-danger">(Rp
                                                    {{ number_format($payroll->pot_toko_alif, 0, ',', '.') }})</span>
                                            </div>

                                            <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                                <span class="fw-bold">Total Potongan</span>
                                                <span class="fw-bold text-danger">Rp
                                                    {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light p-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="mb-0 text-muted small">* This is a digital pay slip. Validated and approved by
                                        HR Department.</p>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <h5 class="mb-0 fw-bold">TOTAL DITERIMA (THP): <span class="text-success ms-2">Rp
                                            {{ number_format($payroll->thp, 0, ',', '.') }}</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection