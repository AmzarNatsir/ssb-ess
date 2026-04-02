@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Pengajuan Resign</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengajuan Resign</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('resign.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="ti ti-circle-plus me-2"></i>Ajukan Resign
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @php
            $isProcessingByHrd = $resigns->contains(function($r) {
                return $r->sts_pengajuan == 2 && ($r->exitInterview && $r->exitInterview->sts_pengajuan == 2);
            });
        @endphp

        @if($isProcessingByHrd)
            <div class="alert alert-info border-start border-4 border-info shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-info-circle-filled me-2 fs-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1 fw-bold">Informasi Pengajuan</h6>
                        <p class="mb-0 small">Pengajuan resign anda saat sedang di proses oleh Admin HRD.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            @forelse($resigns as $resign)
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card flex-fill shadow-sm border">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom pb-2 pt-3">
                        <h6 class="mb-0 fw-bold border-start border-4 border-primary ps-2">Tgl Efektif: {{ \Carbon\Carbon::parse($resign->tgl_eff_resign)->format('d M Y') }}</h6>
                        @php
                            $stClass = 'bg-light';
                            $stLabel = 'Unknown';
                            if($resign->sts_pengajuan == 1) { $stClass = 'bg-light-warning text-warning'; $stLabel = 'Pengajuan'; }
                            if($resign->sts_pengajuan == 2) { $stClass = 'bg-light-success text-success'; $stLabel = 'Disetujui'; }
                            if($resign->sts_pengajuan == 3) { $stClass = 'bg-light-danger text-danger'; $stLabel = 'Ditolak'; }
                            if($resign->sts_pengajuan == 4) { $stClass = 'bg-light-secondary text-secondary'; $stLabel = 'Dibatalkan'; }
                        @endphp
                        <span class="badge {{ $stClass }}">{{ $stLabel }}</span>
                    </div>
                    <div class="card-body py-3">
                        <div class="mb-2">
                            <span class="d-block text-muted small fw-semibold">Tanggal Pengajuan:</span>
                            <span>{{ $resign->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="d-block text-muted small fw-semibold">Alasan:</span>
                            <span class="text-truncate d-block" style="max-width: 100%; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $resign->alasan_resign }}
                            </span>
                        </div>

                        @if($resign->current_approve && $resign->sts_pengajuan == 1)
                        <div class="mb-3 d-flex align-items-center rounded bg-light p-2 border-start border-warning border-3">
                            <div class="ms-1 d-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 32px; height: 32px;">
                                <i class="ti ti-user-check fs-6"></i>
                            </div>
                            <div class="ms-2">
                                <span class="d-block small text-muted fw-semibold" style="font-size: 0.7rem;">APPROVAL SAAT INI</span>
                                <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ $resign->current_approve->nm_lengkap }}</span>
                                <span class="d-block small text-muted" style="font-size: 0.75rem;">{{ $resign->current_approve->jabatan->nm_jabatan ?? '-' }}</span>
                            </div>
                        </div>
                        @endif

                    </div>
                    <div class="card-footer bg-white border-top d-flex flex-wrap gap-2 pt-2 pb-3">
                        <button class="btn btn-sm btn-outline-primary btn-detail flex-fill" data-id="{{ $resign->id }}"><i class="ti ti-eye me-1"></i>Detail</button>
                        @if($resign->is_draft == 1 && $resign->sts_pengajuan == 1)
                            <a href="{{ route('resign.edit', $resign->id) }}" class="btn btn-sm btn-outline-warning flex-fill"><i class="ti ti-edit me-1"></i>Edit</a>
                            <button class="btn btn-sm btn-outline-danger btn-cancel flex-fill" data-id="{{ $resign->id }}"><i class="ti ti-trash me-1"></i>Cancel</button>
                        @elseif($resign->sts_pengajuan == 2 && !$resign->exitInterview)
                            <a href="{{ route('exit-interview.create', $resign->id) }}" class="btn btn-sm btn-outline-dark flex-fill"><i class="ti ti-file me-1"></i>Exits Interview Form</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 py-5 text-center">
                <i class="ti ti-briefcase-off fs-1 text-muted d-block mb-3"></i>
                <h6 class="text-muted">Belum ada pengajuan resign.</h6>
            </div>
            @endforelse
        </div>

        <hr class="my-5 text-muted">

        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Riwayat Exit Interview</h4>
                <p class="text-muted mb-0">Daftar formulir Exit Interview yang telah Anda ajukan.</p>
            </div>
        </div>

        <div class="row">
            @forelse($exitInterviews as $ei)
            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card flex-fill shadow-sm border border-secondary">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom pb-2 pt-3">
                        <h6 class="mb-0 fw-bold border-start border-4 border-dark ps-2">Terkait: Tgl Resign {{ \Carbon\Carbon::parse($ei->getPengajuan->tgl_eff_resign)->format('d M y') }}</h6>
                        @php
                            $stClass = 'bg-light';
                            $stLabel = 'Unknown';
                            if($ei->sts_pengajuan == 1) { $stClass = 'bg-light-warning text-warning'; $stLabel = 'Pengajuan'; }
                            if($ei->sts_pengajuan == 2) { $stClass = 'bg-light-success text-success'; $stLabel = 'Disetujui'; }
                            if($ei->sts_pengajuan == 3) { $stClass = 'bg-light-danger text-danger'; $stLabel = 'Ditolak'; }
                            if($ei->sts_pengajuan == 4) { $stClass = 'bg-light-secondary text-secondary'; $stLabel = 'Dibatalkan'; }
                        @endphp
                        <span class="badge {{ $stClass }}">{{ $stLabel }}</span>
                    </div>
                    <div class="card-body py-3">
                        <div class="mb-2">
                            <span class="d-block text-muted small fw-semibold">Tanggal Mengisi Data:</span>
                            <span>{{ $ei->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($ei->get_current_approve && $ei->sts_pengajuan == 1)
                        <div class="mb-3 d-flex align-items-center rounded bg-light p-2 border-start border-warning border-3">
                            <div class="ms-1 d-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 32px; height: 32px;">
                                <i class="ti ti-user-check fs-6"></i>
                            </div>
                            <div class="ms-2">
                                <span class="d-block small text-muted fw-semibold" style="font-size: 0.7rem;">APPROVAL SAAT INI</span>
                                <span class="d-block fw-bold text-dark" style="font-size: 0.85rem;">{{ $ei->get_current_approve->nm_lengkap }}</span>
                                <span class="d-block small text-muted" style="font-size: 0.75rem;">{{ $ei->get_current_approve->jabatan->nm_jabatan ?? '-' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top d-flex flex-wrap gap-2 pt-2 pb-3">
                        <button class="btn btn-sm btn-outline-primary btn-detail-ei flex-fill" data-id="{{ $ei->id }}"><i class="ti ti-eye me-1"></i>Detail</button>
                        @if($ei->is_draft == 1 && $ei->sts_pengajuan == 1)
                            <a href="{{ route('exit-interview.edit', $ei->id) }}" class="btn btn-sm btn-outline-warning flex-fill"><i class="ti ti-edit me-1"></i>Edit</a>
                            <button class="btn btn-sm btn-outline-danger btn-cancel-ei flex-fill" data-id="{{ $ei->id }}"><i class="ti ti-trash me-1"></i>Cancel</button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 py-5 text-center">
                <i class="ti ti-file-off fs-1 text-muted d-block mb-3"></i>
                <h6 class="text-muted">Belum ada riwayat formulir Exit Interview.</h6>
            </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetailResign" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="ti ti-file-report me-2"></i>Detail Pengajuan Resign</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailResignContent">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Detail Button Click
    $('.btn-detail').on('click', function() {
        const id = $(this).data('id');
        $('#detailResignContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#modalDetailResign').modal('show');

        $.get(`{{ url('/resign') }}/${id}/detail`, function(res) {
            if (res.success) {
                const d = res.data;
                const fileHtml = d.file_surat_resign 
                    ? `<a href="/${d.file_surat_resign}" target="_blank" class="btn btn-outline-danger btn-sm w-100"><i class="ti ti-file-type-pdf me-2"></i>Lihat Surat Resign (PDF)</a>` 
                    : '<span class="text-muted">Tidak ada lampiran</span>';

                let appHtml = '';
                if (d.approvals && d.approvals.length > 0) {
                    appHtml = `
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ti ti-history me-2"></i>History Approval</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Level</th>
                                            <th>Approver</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${d.approvals.map(a => {
                                            let badgeClass = 'bg-light text-muted';
                                            let statusTxt = 'Waiting';
                                            if (a.status == 1) { badgeClass = 'bg-success text-white'; statusTxt = 'Approved'; }
                                            if (a.status == 2) { badgeClass = 'bg-danger text-white'; statusTxt = 'Rejected'; }
                                            if (a.is_active == 1 && a.status == 0) { badgeClass = 'bg-warning text-dark'; statusTxt = 'Current'; }

                                            return `
                                                <tr class="${a.is_active == 1 ? 'table-warning' : ''}">
                                                    <td class="text-center fw-bold">${a.level}</td>
                                                    <td>
                                                        <div class="fw-semibold">${a.name}</div>
                                                        <div class="small text-muted">${a.jabatan}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge ${badgeClass}" style="font-size: 0.7rem;">${statusTxt}</span>
                                                    </td>
                                                    <td class="small text-muted">${a.tgl_app}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                $('#detailResignContent').html(`
                    <div class="d-flex flex-column gap-3">
                        <div class="alert alert-light border mb-0">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <span class="d-block text-muted small fw-semibold mb-1">Tanggal Pengajuan</span>
                                    <span class="fw-semibold d-block text-dark">${d.tgl_pengajuan}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="d-block text-muted small fw-semibold mb-1">Status Utama</span>
                                    <span class="fw-semibold d-block">${d.status_label}</span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="d-block text-muted small fw-semibold mb-1">Tanggal Efektif Resign</span>
                                    <span class="fw-bold fs-5 d-block text-primary">${d.tgl_eff_resign}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="d-block text-muted small fw-semibold mb-1">Alasan Resign</span>
                            <div class="p-3 bg-light rounded text-dark mt-1 border" style="word-wrap: break-word; white-space: pre-wrap; font-size: 0.9rem;">${d.alasan_resign}</div>
                        </div>
                        <div class="pt-2">
                            ${fileHtml}
                        </div>
                        ${appHtml}
                    </div>
                `);
            } else {
                $('#detailResignContent').html('<div class="text-danger text-center">Gagal memuat detail data.</div>');
            }
        }).fail(function() {
            $('#detailResignContent').html('<div class="text-danger text-center">Terjadi kesalahan pada server.</div>');
        });
    });

    // Cancel Button Click
    $('.btn-cancel').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Batalkan Pengajuan Resign?',
            text: "Data pengajuan resign beserta dokumen akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/resign') }}/${id}/cancel`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
    // Detail Exit Interview Button Click
    $('.btn-detail-ei').on('click', function() {
        const id = $(this).data('id');
        // Re-use modal detail but change content
        $('#modalDetailResign .modal-title').html('<i class="ti ti-file-report me-2"></i>Detail Exit Interview');
        $('#detailResignContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#modalDetailResign').modal('show');

        $.get(`{{ url('/exit-interview') }}/${id}/detail`, function(res) {
            if (res.success) {
                const d = res.data;
                const ei = d.detail;

                let appHtml = '';
                if (d.approvals && d.approvals.length > 0) {
                    appHtml = `
                        <div class="alert alert-light border shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="ti ti-history me-2"></i>History Approval Exit Interview</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Level</th>
                                            <th>Approver</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${d.approvals.map(a => {
                                            let badgeClass = 'bg-light text-muted';
                                            let statusTxt = 'Waiting';
                                            if (a.status == 1) { badgeClass = 'bg-success text-white'; statusTxt = 'Approved'; }
                                            if (a.status == 2) { badgeClass = 'bg-danger text-white'; statusTxt = 'Rejected'; }
                                            if (a.is_active == 1 && a.status == 0) { badgeClass = 'bg-warning text-dark'; statusTxt = 'Current'; }

                                            return `
                                                <tr class="${a.is_active == 1 ? 'table-warning' : ''}">
                                                    <td class="text-center fw-bold">${a.level}</td>
                                                    <td>
                                                        <div class="fw-semibold">${a.name}</div>
                                                        <div class="small text-muted">${a.jabatan}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge ${badgeClass}" style="font-size: 0.7rem;">${statusTxt}</span>
                                                    </td>
                                                    <td class="small text-muted">${a.tgl_app}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                let skHtml = `
                    <div class="d-flex flex-column gap-3">
                        <div class="alert alert-light border shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Umum</h6>
                            <div class="row">
                                <div class="col-sm-6 mb-2"><span class="text-muted d-block small">Status</span><span class="fw-semibold">${d.status_label}</span></div>
                                <div class="col-sm-6 mb-2"><span class="text-muted d-block small">Tanggal Isi Form</span><span class="fw-semibold">${d.tgl_pengajuan}</span></div>
                            </div>
                        </div>

                        ${appHtml}

                        <div class="alert alert-light border shadow-sm">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Kumpulan Respon Exit Interview</h6>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">1. Alasan mengundurkan diri:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_1 || '-'}</div>
                                ${ei.jawaban_1_1 ? `<div class="mt-2 text-muted small"><strong>Perusahaan Baru:</strong> ${ei.jawaban_1_1} | <strong>Posisi:</strong> ${ei.jawaban_1_2} | <strong>Gaji:</strong> ${ei.jawaban_1_3}</div>` : ''}
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">2. Pertimbangan pengunduran diri:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_2 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">3. Hal yang tidak sesuai keinginan:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_3 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">4. Kesesuaian Upah dengan kemampuan:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_4 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">5. Kecukupan fasilitas perusahaan:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_5 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">6. Yang paling disukai dari pekerjaan:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_6 || '-'}</div>
                                ${ei.jawaban_6_1 ? `<div class="mt-2 text-muted small"><strong>Posisi awal bergabung :</strong> ${ei.jawaban_6_1}<br><strong>Posisi terakhir :</strong> ${ei.jawaban_6_2}</div>` : ''}
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">7. Kejelasan tugas dan tanggung jawab:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_7 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">8. Penilaian Atasan dan Alasan:</span>
                                <div class="bg-white p-2 border rounded"><strong>Nilai:</strong> ${ei.jawaban_8 || '-'}<br><strong>Alasan:</strong> ${ei.jawaban_8_1 || '-'}</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">9. Saran perbaikan manajemen:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_9 || '-'}</div>
                                <div class="mt-2 text-muted small">
                                    <table class="table table-bordered table-sm mt-2 bg-white">
                                        <tr><th width="70%">Kenyamanan Kerja</th><td>${ei.jawaban_9_1}</td></tr>
                                        <tr><th>Sistem Kerja</th><td>${ei.jawaban_9_2}</td></tr>
                                        <tr><th>Gaji & Tunjangan</th><td>${ei.jawaban_9_3}</td></tr>
                                        <tr><th>Kesempatan Berkembang</th><td>${ei.jawaban_9_4}</td></tr>
                                        <tr><th>Efektivitas Organisasi</th><td>${ei.jawaban_9_5}</td></tr>
                                        <tr><th>Kebijakan Asuransi</th><td>${ei.jawaban_9_6}</td></tr>
                                        <tr><th>Perhatian Manajemen</th><td>${ei.jawaban_9_7}</td></tr>
                                        <tr><th>Lingkungan Kerja</th><td>${ei.jawaban_9_8}</td></tr>
                                        <tr><th>Fasilitas Penunjang</th><td>${ei.jawaban_9_9}</td></tr>
                                    </table>
                                </div>
                                <div class="mt-2 text-center text-white p-1" style="background-color: #f7504f; font-size: 0.85rem; border-radius: 4px;">
                                    1 = Sangat Kurang | 2 = Kurang | 3 = Baik | 4 = Sangat Baik
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="d-block fw-semibold mb-1">10. Komentar Opsional:</span>
                                <div class="bg-white p-2 border rounded">${ei.jawaban_10 || '-'}</div>
                            </div>
                        </div>
                    </div>
                `;

                $('#detailResignContent').html(skHtml);
            } else {
                $('#detailResignContent').html('<div class="text-danger text-center">Gagal memuat detail data.</div>');
            }
        }).fail(function() {
            $('#detailResignContent').html('<div class="text-danger text-center">Terjadi kesalahan pada server.</div>');
        });
    });

    // Cancel Exit Interview Click
    $('.btn-cancel-ei').on('click', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Batalkan Form Exit Interview?',
            text: "Data form akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/exit-interview') }}/${id}/cancel`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
