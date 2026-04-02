@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Pinjaman Karyawan</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Pinjaman</li>
                    </ol>
                </nav>
            </div>
            @if($hasActiveLoan ?? false)
                <button class="btn btn-secondary d-inline-flex align-items-center cursor-not-allowed" disabled title="Anda masih memiliki pinjaman aktif">
                    <i class="ti ti-lock me-2"></i>Ajukan Pinjaman
                </button>
            @else
                <a href="{{ route('pinjaman.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="ti ti-circle-plus me-2"></i>Ajukan Pinjaman
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title mb-0"><i class="ti ti-file-dollar me-2 text-primary"></i>Riwayat Pinjaman</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Tgl Pengajuan</th>
                                <th>Jenis Pinjaman</th>
                                <th>Alasan</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center">Tenor</th>
                                <th class="text-end">Angsuran/Bln</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pinjamans as $i => $p)
                            <tr>
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td>{{ date('d M Y', strtotime($p->tgl_pengajuan)) }}</td>
                                <td>
                                    @if($p->kategori == 1)
                                        <span class="badge bg-soft-info text-info fw-semibold">Panjar Gaji</span>
                                    @else
                                        <span class="badge bg-soft-primary text-primary fw-semibold">PKK</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width:160px;">{{ $p->alasan_pengajuan }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($p->nominal_apply, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $p->tenor_apply }} bln</td>
                                <td class="text-end">Rp {{ number_format($p->angsuran, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            1 => ['label'=>'Pengajuan',  'class'=>'bg-light-warning text-warning'],
                                            2 => ['label'=>'Disetujui',  'class'=>'bg-light-success text-success'],
                                            3 => ['label'=>'Ditolak',    'class'=>'bg-light-danger text-danger'],
                                            4 => ['label'=>'Dibatalkan', 'class'=>'bg-light-secondary text-secondary'],
                                        ];
                                        $st = $statusMap[$p->status_pengajuan] ?? ['label'=>'?','class'=>'bg-light'];
                                    @endphp
                                    <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                                    @if($p->status_pengajuan == 1 && $p->currentApprover)
                                        <div class="small mt-1 text-muted fw-semibold" style="font-size: 0.70rem; line-height: 1;">
                                            <i class="ti ti-user-check me-1"></i>{{ $p->currentApprover->nm_lengkap ?? $p->currentApprover->nm_karyawan }}
                                            @if($p->currentApprover->jabatan)
                                            <div class="mt-1 opacity-75 fw-normal" style="font-size: 0.65rem;">{{ $p->currentApprover->jabatan->nm_jabatan }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-success btn-detail" data-id="{{ $p->id }}" data-kategori="{{ $p->kategori }}" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @if($p->is_draft == 1 && $p->status_pengajuan == 1)
                                    <button class="btn btn-sm btn-outline-danger btn-cancel ms-1" data-id="{{ $p->id }}" title="Batalkan Pengajuan">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-file-off fs-2 d-block mb-2"></i>
                                    Belum ada pengajuan pinjaman.
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetailPinjaman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="ti ti-file-dollar me-2"></i>Detail Pinjaman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detailPinjamanContent">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '.btn-detail', function() {
    const id = $(this).data('id');
    $('#detailPinjamanContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
    $('#modalDetailPinjaman').modal('show');

    $.get(`{{ url('/pinjaman') }}/${id}/detail`, function(res) {
        if (res.success) {
            const d = res.data;
            let mutasiRows = '';
            (d.mutasi || []).forEach((m, i) => {
                const aktif = m.bayar_aktif ? '<span class="badge bg-light-warning text-warning">Aktif</span>' : '<span class="badge bg-light-secondary text-secondary">Menunggu</span>';
                const bayar = m.status ? '<span class="badge bg-light-success text-success">Lunas</span>' : aktif;
                mutasiRows += `<tr><td>${i+1}</td><td>${m.tanggal}</td><td class="text-end">Rp ${Number(m.nominal).toLocaleString('id')}</td><td class="text-center">${bayar}</td></tr>`;
            });

            let docsHtml = '';
            if (d.dokumen && d.dokumen.length > 0) {
                d.dokumen.forEach(doc => {
                    docsHtml += `<a href="/${doc.file}" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1"><i class="ti ti-paperclip me-1"></i>${doc.keterangan || 'Dokumen'}</a>`;
                });
            }

            let pembayaranRows = '';
            if (d.pembayaran && d.pembayaran.length > 0) {
                d.pembayaran.forEach((p, i) => {
                    pembayaranRows += `<tr><td>${i+1}</td><td>${p.tanggal}</td><td class="text-end text-success fw-semibold">Rp ${Number(p.nominal).toLocaleString('id')}</td></tr>`;
                });
            } else {
                pembayaranRows = '<tr><td colspan="3" class="text-center text-muted">Belum ada riwayat pembayaran</td></tr>';
            }

            $('#detailPinjamanContent').html(`
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-info-circle me-2"></i>Info Pinjaman</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:45%">Jenis</td><td class="fw-semibold">${d.kategori_label}</td></tr>
                                    <tr><td class="text-muted">Alasan</td><td class="fw-semibold">${d.alasan_pengajuan}</td></tr>
                                    <tr><td class="text-muted">Tgl Pengajuan</td><td>${d.tgl_pengajuan}</td></tr>
                                    <tr><td class="text-muted">Status</td><td>${d.status_label}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-calculator me-2"></i>Rincian Saldo & Tenor</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:45%">Nominal Awal</td><td class="fw-semibold">Rp ${Number(d.nominal_apply).toLocaleString('id')}</td></tr>
                                    <tr><td class="text-muted">Total Bayar</td><td class="fw-semibold text-success">Rp ${Number(d.total_payment).toLocaleString('id')}</td></tr>
                                    <tr class="border-top"><td class="text-muted">Outstanding</td><td class="fw-bold text-danger">Rp ${Number(d.outstanding).toLocaleString('id')}</td></tr>
                                    <tr><td class="text-muted pt-2">Tenor Awal</td><td class="pt-2">${d.tenor_apply} Bulan</td></tr>
                                    <tr><td class="text-muted">Sisa Tenor</td><td class="fw-semibold text-primary">${d.sisa_tenor} Bulan</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                ${docsHtml ? `<div class="mb-3"><h6 class="fw-bold text-primary"><i class="ti ti-paperclip me-2"></i>Dokumen</h6>${docsHtml}</div>` : ''}
                
                <div class="row mt-2">
                    <div class="col-md-5">
                        <h6 class="fw-bold text-success mb-2"><i class="ti ti-receipt me-2"></i>History Pembayaran</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="bg-light"><tr><th>No</th><th>Tanggal</th><th class="text-end">Nominal Bayar</th></tr></thead>
                                <tbody>${pembayaranRows}</tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-bold text-primary mb-2"><i class="ti ti-list me-2"></i>Jadwal Angsuran (Restrukturisasi)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="bg-light"><tr><th>No</th><th>Bln Tagihan</th><th class="text-end">Nominal Tagihan</th><th class="text-center">Status</th></tr></thead>
                                <tbody>${mutasiRows}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `);
        }
    });
});

$(document).on('click', '.btn-cancel', function() {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Batalkan Pengajuan?',
        text: "Data pengajuan pinjaman beserta dokumen akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('/pinjaman') }}/${id}/cancel`,
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
</script>
@endsection
