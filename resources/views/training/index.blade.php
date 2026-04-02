@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Pelatihan</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pelatihan</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-bottom mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#current_year" role="tab" aria-selected="true">
                        <i class="ti ti-calendar-event me-2"></i>Pelatihan Tahun Ini
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab" aria-selected="false">
                        <i class="ti ti-history me-2"></i>History Pelatihan
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Current Year Tab -->
                <div class="tab-pane fade show active" id="current_year" role="tabpanel">
                    @include('training.partials.table', ['trainings' => $currentYearTrainings])
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                    @include('training.partials.table', ['trainings' => $historyTrainings])
                </div>
            </div>
        </div>
    </div>

<!-- Modal Detail Pelatihan -->
<div class="modal fade" id="modalDetailTraining" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="ti ti-file-description me-2"></i>Detail Pelatihan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="training-detail-content">
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

<!-- Modal Laporan Setelah Pelatihan -->
<div class="modal fade" id="modalReportTraining" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="ti ti-file-report me-2"></i>Laporan Setelah Pelatihan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formReportTraining" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="training_id" id="report_training_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tujuan Pelatihan</label>
                        <textarea class="form-control" name="tujuan_pelatihan_pasca" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Uraian Materi Pelatihan</label>
                        <textarea class="form-control" name="uraian_materi_pasca" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tindak Lanjut Setelah Mengikuti Pelatihan</label>
                        <textarea class="form-control" name="tindak_lanjut_pasca" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dampak Setelah Mengikuti Pelatihan</label>
                        <textarea class="form-control" name="dampak_pasca" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Penutup/Harapan</label>
                        <textarea class="form-control" name="penutup_pasca" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Evidence (Bukti)</label>
                        <!-- Preview for existing evidence -->
                        <div id="existingEvidencePreview" class="mb-2" style="display:none;">
                            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded border">
                                <i class="ti ti-photo text-success fs-18" id="evidenceIcon"></i>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">File yang tersimpan:</small>
                                    <a id="evidenceLink" href="#" target="_blank" class="fw-semibold small text-primary">Lihat File</a>
                                </div>
                            </div>
                            <div id="evidenceImagePreview" class="mt-2" style="display:none;">
                                <img id="evidenceImageTag" src="" alt="Evidence Preview" class="img-fluid rounded border" style="max-height:200px; object-fit:contain;">
                            </div>

                        </div>
                        <input type="file" class="form-control" name="evidence_pasca" id="evidenceFileInput" accept=".pdf,.png,.jpg,.jpeg">
                        <small class="text-muted">Format yang diizinkan: PDF, PNG, JPG. Maks 2MB. Kosongkan jika tidak ingin mengubah file.</small>
                        <!-- Preview for newly selected file -->
                        <div id="newEvidencePreview" class="mt-2" style="display:none;">
                            <img id="newEvidenceImageTag" src="" alt="Preview" class="img-fluid rounded border" style="max-height:180px; object-fit:contain;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSaveReport">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" style="position:fixed; top:20px; right:20px; z-index:9999; min-width:300px;"></div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // CSRF Setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle Detail Button Click
    $(document).on('click', '.btn-detail-training', function() {
        const id = $(this).data('id');
        $('#modalDetailTraining').modal('show');
        $('#training-detail-content').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.ajax({
            url: `{{ url('/training') }}/${id}/detail`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let pesertaHtml = '';
                    
                    if (data.peserta && data.peserta.length > 0) {
                        data.peserta.forEach((p, index) => {
                            pesertaHtml += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${p.nik}</td>
                                    <td class="fw-medium">${p.name}</td>
                                    <td>${p.jabatan}</td>
                                </tr>
                            `;
                        });
                    } else {
                        pesertaHtml = '<tr><td colspan="4" class="text-center py-3 text-muted">Belum ada peserta yang terdaftar</td></tr>';
                    }

                    $('#training-detail-content').html(`
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-info-circle me-2"></i>Informasi Pelatihan</h6>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Nomor</label>
                                            <span class="fw-semibold">${data.nomor}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Nama Pelatihan</label>
                                            <span class="fw-semibold">${data.nama_pelatihan}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Lembaga / Vendor</label>
                                            <span class="fw-semibold">${data.vendor}</span>
                                        </div>
                                        <div>
                                            <label class="text-muted small d-block">Kategori</label>
                                            <span class="badge bg-soft-info text-info">${data.kategori}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 text-primary"><i class="ti ti-calendar me-2"></i>Waktu & Tempat</h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Tanggal Pelaksanaan</label>
                                                <span class="fw-semibold small">${data.tanggal_awal} s/d ${data.tanggal_sampai}</span>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Waktu (Pukul)</label>
                                                <span class="fw-semibold small">${data.pukul_awal} - ${data.pukul_sampai}</span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="text-muted small d-block">Durasi Pelatihan</label>
                                            <span class="badge bg-soft-primary text-primary">${data.durasi} Jam</span>
                                        </div>
                                        <div class="mt-3">
                                            <label class="text-muted small d-block">Tempat Pelaksanaan</label>
                                            <span class="fw-semibold">${data.tempat}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 mt-2 text-primary border-bottom pb-2"><i class="ti ti-users me-2"></i>Daftar Peserta</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width:50px;">No</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${pesertaHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('#training-detail-content').html(`<div class="alert alert-danger">Gagal mengambil data detail: ${response.message || 'Unknown error'}</div>`);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#training-detail-content').html(`<div class="alert alert-danger">Terjadi kesalahan saat memproses permintaan. Silakan cek konsol browser atau log server.</div>`);
            }
        });
    });

    // Handle Report Button Click — fetch existing data first
    $(document).on('click', '.btn-report-training', function() {
        const id = $(this).data('id');
        $('#report_training_id').val(id);
        // Reset form and preview areas
        $('#formReportTraining')[0].reset();
        $('#existingEvidencePreview').hide();
        $('#evidenceImagePreview').hide();
        $('#newEvidencePreview').hide();

        $('#modalReportTraining').modal('show');

        // Fetch existing report data
        $.ajax({
            url: `{{ url('/training') }}/${id}/get-report`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.has_report && response.data) {
                    const d = response.data;
                    // Pre-fill text fields
                    $('[name="tujuan_pelatihan_pasca"]').val(d.tujuan_pelatihan_pasca || '');
                    $('[name="uraian_materi_pasca"]').val(d.uraian_materi_pasca || '');
                    $('[name="tindak_lanjut_pasca"]').val(d.tindak_lanjut_pasca || '');
                    $('[name="dampak_pasca"]').val(d.dampak_pasca || '');
                    $('[name="penutup_pasca"]').val(d.penutup_pasca || '');

                    // Show evidence preview if file exists
                    if (d.evidence_url) {
                        $('#evidenceLink').attr('href', d.evidence_url);
                        $('#existingEvidencePreview').show();
                        if (d.is_image) {
                            $('#evidenceImageTag').attr('src', d.evidence_url);
                            $('#evidenceIcon').attr('class', 'ti ti-photo text-success fs-18');
                            $('#evidenceImagePreview').show();
                        } else {
                            $('#evidenceIcon').attr('class', 'ti ti-file-type-pdf text-danger fs-18');
                            $('#evidenceImagePreview').hide();
                        }
                    }
                }
            }
        });
    });

    // Preview newly selected image file
    $(document).on('change', '#evidenceFileInput', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#newEvidenceImageTag').attr('src', e.target.result);
                $('#newEvidencePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#newEvidencePreview').hide();
        }
    });


    // ── Toast Helper ─────────────────────────────────────────────
    function showToast(message, type) {
        type = type || 'success';
        const colors = {
            success: { bg: '#198754', icon: 'ti-circle-check' },
            danger:  { bg: '#dc3545', icon: 'ti-circle-x' },
            warning: { bg: '#ffc107', icon: 'ti-alert-triangle' },
        };
        const c = colors[type] || colors.success;
        const id = 'toast_' + Date.now();
        const autoHide = (type === 'success');
        const html = `
            <div id="${id}" class="d-flex align-items-center text-white rounded shadow-lg mb-2 px-3 py-3"
                 style="background:${c.bg}; animation: slideInRight .3s ease; min-width:280px;">
                <i class="ti ${c.icon} fs-20 me-3 flex-shrink-0"></i>
                <div class="flex-grow-1 fw-semibold" style="font-size:.93rem;">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-3 flex-shrink-0" onclick="document.getElementById('${id}').remove()" style="filter:brightness(2);"></button>
            </div>`;
        $('#toastContainer').prepend(html);
        if (autoHide) {
            setTimeout(function() { $('#' + id).fadeOut(400, function() { $(this).remove(); }); }, 3000);
        }
    }

    // Handle Report Form Submit
    $('#formReportTraining').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let trainingId = $('#report_training_id').val();
        let btn = $('#btnSaveReport');
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

        $.ajax({
            url: `{{ url('/training') }}/${trainingId}/report`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false).html('Simpan Laporan');
                if (response.success) {
                    $('#modalReportTraining').modal('hide');
                    showToast(response.message || 'Laporan berhasil disimpan.', 'success');
                    setTimeout(function() { location.reload(); }, 2000);
                } else {
                    showToast(response.message || 'Terjadi kesalahan saat menyimpan.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                btn.prop('disabled', false).html('Simpan Laporan');
                showToast('Terjadi kesalahan koneksi atau server.', 'danger');
            }
        });
    });
});
</script>

<style>
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(60px); }
    to   { opacity: 1; transform: translateX(0); }
}
</style>
@endsection
