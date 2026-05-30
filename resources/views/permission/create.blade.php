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
                    <h4 class="mb-1">Apply Permission</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{route('permission.index')}}">Permission</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Apply Permission</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-0">
                            <h5 class="mb-0 fw-semibold">
                                <i class="ti ti-file-text me-2 text-primary"></i>Permission Request Form
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('permission.store') }}" method="POST" id="permissionForm">
                                @csrf

                                <!-- Jenis Izin -->
                                <div class="mb-4">
                                    <label for="id_jenis_izin" class="form-label fw-medium">
                                        Jenis Izin <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('id_jenis_izin') is-invalid @enderror"
                                            id="id_jenis_izin"
                                            name="id_jenis_izin"
                                            required>
                                        <option value="">-- Pilih Jenis Izin --</option>
                                        @foreach($permissionTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('id_jenis_izin') == $type->id ? 'selected' : '' }}>
                                                {{ $type->nm_jenis_ci }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_jenis_izin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tanggal Mulai -->
                                <div class="mb-4">
                                    <label for="tgl_awal" class="form-label fw-medium">
                                        Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('tgl_awal') is-invalid @enderror"
                                           id="tgl_awal"
                                           name="tgl_awal"
                                           value="{{ old('tgl_awal') }}"
                                           required>
                                    @error('tgl_awal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tanggal Selesai -->
                                <div class="mb-4">
                                    <label for="tgl_akhir" class="form-label fw-medium">
                                        Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('tgl_akhir') is-invalid @enderror"
                                           id="tgl_akhir"
                                           name="tgl_akhir"
                                           value="{{ old('tgl_akhir') }}"
                                           required>
                                    @error('tgl_akhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jumlah Hari (Readonly) -->
                                <div class="mb-4">
                                    <label for="jumlah_hari" class="form-label fw-medium">
                                        Jumlah Hari
                                    </label>
                                    <input type="text"
                                           class="form-control bg-light"
                                           id="jumlah_hari"
                                           name="jumlah_hari"
                                           value="0"
                                           readonly>
                                    <small class="text-muted">Calculated automatically based on start and end date</small>
                                </div>

                                <!-- Keterangan/Alasan -->
                                <div class="mb-4">
                                    <label for="ket_izin" class="form-label fw-medium">
                                        Keterangan / Alasan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('ket_izin') is-invalid @enderror"
                                              id="ket_izin"
                                              name="ket_izin"
                                              rows="4"
                                              placeholder="Masukkan alasan pengajuan izin..."
                                              required>{{ old('ket_izin') }}</textarea>
                                    @error('ket_izin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('permission.index') }}" class="btn btn-light">
                                        <i class="ti ti-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit"
                                            class="btn btn-primary"
                                            {{ !($isApprovalMatrixConfigured ?? false) ? 'disabled' : '' }}
                                            title="{{ !($isApprovalMatrixConfigured ?? false) ? 'Matriks persetujuan izin belum diatur.' : '' }}">
                                        <i class="ti ti-send me-1"></i>Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light border-0">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ti ti-info-circle me-2 text-info"></i>Information
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(($isApprovalMatrixConfigured ?? false) === true)
                                <div class="alert alert-success border-0 mb-3">
                                    <h6 class="alert-heading fw-semibold mb-1">
                                        <i class="ti ti-shield-check me-1"></i>Validasi Matriks Persetujuan
                                    </h6>
                                    <p class="small mb-0">Matriks persetujuan untuk pengajuan izin sudah diatur. Pengajuan dapat dikirim.</p>
                                </div>
                            @else
                                <div class="alert alert-danger border-0 mb-3">
                                    <h6 class="alert-heading fw-semibold mb-1">
                                        <i class="ti ti-alert-triangle me-1"></i>Validasi Matriks Persetujuan
                                    </h6>
                                    <p class="small mb-0">Matriks persetujuan untuk pengajuan izin belum diatur. Tombol submit dikunci.</p>
                                </div>
                            @endif

                            <div class="alert alert-info border-0 mb-3">
                                <h6 class="alert-heading fw-semibold">
                                    <i class="ti ti-bulb me-1"></i>Tips
                                </h6>
                                <ul class="mb-0 ps-3">
                                    <li class="small mb-2">Pastikan tanggal yang dipilih sudah benar</li>
                                    <li class="small mb-2">Jumlah hari akan dihitung otomatis</li>
                                    <li class="small">Berikan alasan yang jelas untuk mempercepat approval</li>
                                </ul>
                            </div>

                            <div class="border-top pt-3">
                                <h6 class="fw-semibold mb-3">Employee Information</h6>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Name</small>
                                    <p class="mb-0 fw-medium">{{ $karyawan->nm_lengkap }}</p>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Position</small>
                                    <p class="mb-0">{{ $karyawan->jabatan->nm_jabatan ?? '-' }}</p>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block mb-1">Department</small>
                                    <p class="mb-0">{{ $karyawan->departemen->nm_dept ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startInput = document.getElementById('tgl_awal');
    const endInput = document.getElementById('tgl_akhir');
    const totalInput = document.getElementById('jumlah_hari');

    function parseLocalDate(dateString) {
        const parts = dateString.split('-').map(Number);
        if (parts.length !== 3 || parts.some(Number.isNaN)) {
            return null;
        }

        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function calculateDays() {
        const startValue = startInput.value;
        const endValue = endInput.value;

        if (!startValue || !endValue) {
            totalInput.value = '0';
            return;
        }

        const startDate = parseLocalDate(startValue);
        const endDate = parseLocalDate(endValue);

        if (!startDate || !endDate || endDate < startDate) {
            totalInput.value = '0';
            return;
        }

        const oneDayMs = 24 * 60 * 60 * 1000;
        const diffDays = Math.floor((endDate - startDate) / oneDayMs) + 1;
        totalInput.value = String(diffDays);
    }

    startInput.addEventListener('change', calculateDays);
    endInput.addEventListener('change', calculateDays);
    startInput.addEventListener('input', calculateDays);
    endInput.addEventListener('input', calculateDays);

    calculateDays();
});
</script>
