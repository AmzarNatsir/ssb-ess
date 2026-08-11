@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Pengajuan Cuti / Leave Request</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{route('leave.index')}}">Cuti</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ajukan Cuti</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Pengajuan Cuti</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(!$isEligible)
                                <div class="alert alert-danger border-start border-danger border-3 mb-4" role="alert">
                                    <div class="fw-semibold mb-1">Anda belum bisa mengajukan cuti</div>
                                    <div class="small mb-0">Pengajuan cuti hanya dapat dilakukan oleh karyawan yang telah bekerja minimal <strong>1 tahun</strong>. Silakan hubungi HRD/Administrator untuk informasi lebih lanjut.</div>
                                </div>
                            @elseif(!$isApprovalMatrixConfigured)
                                <div class="alert alert-danger border-start border-danger border-3 mb-4" role="alert">
                                    <div class="fw-semibold mb-1">Matriks Persetujuan Belum Diatur</div>
                                    <div class="small mb-0">Pengajuan cuti belum dapat dikirim karena approval matrix departemen Anda belum tersedia. Hubungi HRD/Administrator untuk pengaturan matriks approval.</div>
                                </div>
                            @else
                                <div class="alert alert-success border-start border-success border-3 mb-4" role="alert">
                                    <div class="fw-semibold mb-2">Informasi Matriks Persetujuan / Approval Matrix</div>
                                    <div class="small mb-2">Pengajuan cuti akan diproses sesuai level approval yang sudah diatur untuk departemen Anda.</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($approvalMatrix as $approval)
                                            <span class="badge bg-light text-dark border">
                                                Level {{ $approval->approval_level }}: {{ $approval->getPejabat->nm_lengkap ?? 'Belum terisi' }}
                                                @if(optional($approval->getPejabat)->jabatan)
                                                    ({{ $approval->getPejabat->jabatan->nm_jabatan }})
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($isEligible)
                            <form action="{{ route('leave.store') }}" method="POST" id="leaveForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Jenis Cuti / Leave Type <span class="text-danger">*</span></label>
                                        <select name="id_jenis_cuti" id="id_jenis_cuti" class="form-control @error('id_jenis_cuti') is-invalid @enderror" required>
                                            <option value="">-- Pilih Jenis Cuti --</option>
                                            @foreach($leaveTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('id_jenis_cuti') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->nm_jenis_ci }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_jenis_cuti')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Mulai / Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="tgl_awal" id="tgl_awal" class="form-control @error('tgl_awal') is-invalid @enderror" value="{{ old('tgl_awal') }}" required>
                                        @error('tgl_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Selesai / End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control @error('tgl_akhir') is-invalid @enderror" value="{{ old('tgl_akhir') }}" required>
                                        @error('tgl_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jumlah Hari / Total Days</label>
                                        <input type="text" id="jumlah_hari" class="form-control bg-light" readonly placeholder="0 Hari">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sisa Hak Cuti / Remaining Entitlement</label>
                                        <input type="text" id="remaining_entitlement" class="form-control bg-light" readonly placeholder="-">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Keterangan / Reason <span class="text-danger">*</span></label>
                                        <textarea name="ket_cuti" class="form-control @error('ket_cuti') is-invalid @enderror" rows="3" required>{{ old('ket_cuti') }}</textarea>
                                        @error('ket_cuti')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('leave.index') }}" class="btn btn-light">Batal / Cancel</a>
                                    <button type="submit" class="btn btn-primary" {{ !$isApprovalMatrixConfigured ? 'disabled' : '' }}>Ajukan Cuti / Submit</button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const leaveTypeSelect = document.getElementById('id_jenis_cuti');
            const startDateInput = document.getElementById('tgl_awal');
            const endDateInput = document.getElementById('tgl_akhir');
            const jumlahHariInput = document.getElementById('jumlah_hari');
            const remainingInput = document.getElementById('remaining_entitlement');

            function parseLocalDate(value) {
                if (!value) {
                    return null;
                }

                const parts = value.split('-').map(Number);
                if (parts.length !== 3 || parts.some(Number.isNaN)) {
                    return null;
                }

                return new Date(parts[0], parts[1] - 1, parts[2]);
            }

            function calculateDays() {
                const start = parseLocalDate(startDateInput.value);
                const end = parseLocalDate(endDateInput.value);

                if (start && end) {
                    if (end >= start) {
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        jumlahHariInput.value = diffDays + ' Hari';
                    } else {
                        jumlahHariInput.value = 'Periode tidak valid';
                    }
                } else {
                    jumlahHariInput.value = '0 Hari';
                }
            }

            function fetchEntitlement() {
                const id = leaveTypeSelect.value;
                if (id) {
                    fetch(`/leave/remaining-entitlement/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            remainingInput.value = data.remaining + ' Hari';
                        })
                        .catch(error => {
                            console.error('Error fetching entitlement:', error);
                            remainingInput.value = 'Gagal memuat data';
                        });
                } else {
                    remainingInput.value = '-';
                }
            }

            leaveTypeSelect.addEventListener('change', fetchEntitlement);
            startDateInput.addEventListener('change', calculateDays);
            endDateInput.addEventListener('change', calculateDays);

            // Initial calculation if values exist (e.g., after validation error)
            if (leaveTypeSelect.value) fetchEntitlement();
            if (startDateInput.value && endDateInput.value) calculateDays();
        });
    </script>

@endsection
