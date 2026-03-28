@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Memorandum</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Memorandum</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Period Filter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('memorandum.index') }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="form-label mb-0 fw-semibold">Period Filter:</label>
                            </div>
                            <div class="col-md-3">
                                <select name="month" class="form-select" onchange="this.form.submit()">
                                    @foreach($months as $value => $name)
                                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="year" class="form-select" onchange="this.form.submit()">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('memorandum.index') }}" class="btn btn-light" title="Reset Filter">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-bottom mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#warning_letter" role="tab" aria-selected="true">
                        <i class="ti ti-file-text me-2"></i>Surat Peringatan (SP)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#reprimand_letter" role="tab" aria-selected="false">
                        <i class="ti ti-alert-circle me-2"></i>Surat Teguran (ST)
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Warning Letter (SP) Tab -->
                <div class="tab-pane fade show active" id="warning_letter" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No.</th>
                                            <th>Nomor Surat</th>
                                            <th>Jenis SP</th>
                                            <th>Uraian Pelanggaran</th>
                                            <th>Masa Berlaku</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($warningLetters as $index => $sp)
                                            <tr>
                                                <td class="ps-4 fw-medium">{{ $index + 1 }}</td>
                                                <td><span class="text-dark fw-semibold">{{ $sp->no_sp }}</span>
                                                <br>{{ date('d M Y', strtotime($sp->tgl_sp)) }}
                                            </td>
                                                <td>
                                                    <span class="badge badge-soft-danger badge-sm">
                                                        {{ $sp->jenisSpDisetujui->nm_jenis_sp ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-wrap" style="max-width: 300px;">{{ $sp->uraian_pelanggaran }}</td>
                                                <td class="text-wrap" style="max-width: 300px;">
                                                <span class="badge badge-soft-danger badge-sm">{{ $sp->sp_lama_active }} Bulan</span>
                                                <br>
                                                {{ date('d M Y', strtotime($sp->sp_mulai_active)) ." s/d ". date('d M Y', strtotime($sp->sp_akhir_active)) }}</td>
                                                <td>
                                                    <span class="badge badge-soft-success badge-sm">
                                                        {{ $sp->karyawan->sp_active == "active" ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('memorandum.print_sp', $sp->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="ti ti-printer me-1"></i>Cetak
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                                        <p class="mb-0">Tidak ada Surat Peringatan ditemukan.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reprimand Letter (ST) Tab -->
                <div class="tab-pane fade" id="reprimand_letter" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">No.</th>
                                            <th>Tanggal Kejadian</th>
                                            <th>Jam Kejadian</th>
                                            <th>Jenis Pelanggaran</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reprimandLetters as $index => $st)
                                            <tr>
                                                <td class="ps-4 fw-medium">{{ $index + 1 }}</td>
                                                <td>{{ date('d M Y', strtotime($st->tanggal_kejadian)) }}</td>
                                                <td><span class="text-dark">{{ date('H:i', strtotime($st->waktu_kejadian)) }}</span></td>
                                                <td>
                                                    <span class="badge badge-soft-warning badge-sm">
                                                        {{ $st->jenisPelanggaran->jenis_pelanggaran ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-info btn-detail-st" data-id="{{ $st->id }}">
                                                            <i class="ti ti-eye me-1"></i>Detail
                                                        </button>
                                                        <a href="{{ route('memorandum.print_st', $st->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                            <i class="ti ti-printer me-1"></i>Cetak
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                                        <p class="mb-0">Tidak ada Surat Teguran ditemukan.</p>
                                                    </div>
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

        </div>
    </div>

    <!-- Modal Detail Surat Teguran -->
    <div class="modal fade" id="modalDetailST" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="ti ti-file-description me-2"></i>Detail Surat Teguran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="st-detail-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-detail-st', function() {
        const id = $(this).data('id');
        console.log('Button clicked for ID:', id);
        // alert('Loading detail for ID: ' + id); // Temporary alert for debugging
        
        $('#modalDetailST').modal('show');
        $('#st-detail-content').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: `{{ url('/memorandum/st-detail') }}/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let approvalHtml = '';
                    
                    if (data.approvals && data.approvals.length > 0) {
                        data.approvals.forEach(appr => {
                            const statusClass = appr.status === 'Processed' ? 'text-success' : (appr.status === 'Pending' ? 'text-primary' : 'text-muted');
                            approvalHtml += `
                                <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold">${appr.name}</h6>
                                        <span class="badge ${appr.status === 'Processed' ? 'bg-soft-success text-success' : 'bg-soft-primary text-primary'} badge-sm">${appr.status}</span>
                                    </div>
                                    <div class="text-muted small">${appr.position}</div>
                                    <div class="mt-2 small">
                                        <span class="text-muted">Status:</span> <span class="${statusClass} fw-medium">${appr.label_status}</span>
                                        <span class="mx-2 text-muted">|</span>
                                        <span class="text-muted">Tanggal:</span> <span class="text-dark">${appr.date}</span>
                                    </div>
                                    ${appr.remark && appr.remark !== '-' ? `<div class="mt-2 p-2 bg-light rounded small text-dark italic">"${appr.remark}"</div>` : ''}
                                </div>
                            `;
                        });
                    } else {
                        approvalHtml = '<div class="alert alert-info small py-2">Belum ada riwayat approval.</div>';
                    }

                    $('#st-detail-content').html(`
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-info-circle me-2"></i>Informasi Kejadian</h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Tanggal</label>
                                                <span class="fw-semibold">${data.tanggal_kejadian}</span>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Waktu</label>
                                                <span class="fw-semibold">${data.waktu_kejadian}</span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="text-muted small d-block">Tempat</label>
                                            <span class="fw-semibold">${data.tempat_kejadian || '-'}</span>
                                        </div>
                                        <div class="mt-3">
                                            <label class="text-muted small d-block">Jenis Pelanggaran</label>
                                            <span class="badge bg-soft-warning text-warning">${data.jenis_pelanggaran}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-list-details me-2"></i>Detail & Tindakan</h6>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Akibat Pelanggaran</label>
                                            <p class="mb-0 text-dark small">${data.akibat || '-'}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Tindakan Diambil</label>
                                            <p class="mb-0 text-dark small">${data.tindakan || '-'}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Rekomendasi</label>
                                            <p class="mb-0 text-dark small">${data.rekomendasi || '-'}</p>
                                        </div>
                                        <div>
                                            <label class="text-muted small d-block">Komentar Pelanggar</label>
                                            <p class="mb-0 text-dark small">${data.komentar_pelanggar || '-'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 mt-2 text-primary border-bottom pb-2"><i class="ti ti-users me-2"></i>Riwayat Approval</h6>
                                <div class="list-group list-group-flush">
                                    ${approvalHtml}
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('#st-detail-content').html(`<div class="alert alert-danger">Gagal mengambil data detail: ${response.message || 'Unknown error'}</div>`);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                $('#st-detail-content').html(`<div class="alert alert-danger">Terjadi kesalahan saat memproses permintaan. Silakan cek konsol browser atau log server.</div>`);
            }
        });
    });
});
</script>
@endsection
