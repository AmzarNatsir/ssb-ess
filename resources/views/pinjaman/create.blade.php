@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Ajukan Pinjaman</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
                        <li class="breadcrumb-item active">Pengajuan Baru</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Employee Profile Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="d-flex align-items-stretch flex-wrap">

                    <!-- Avatar + Name block -->
                    <div class="d-flex align-items-center gap-3 p-4 border-end flex-shrink-0" style="min-width:260px;">
                        @php
                            $foto = $karyawan->foto ?? null;
                            $initials = collect(explode(' ', $karyawan->nm_lengkap ?? 'U'))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                        @endphp
                        @if($foto && file_exists(public_path($foto)))
                            <img src="{{ asset($foto) }}" alt="Foto"
                                 class="rounded-circle border border-2 border-primary"
                                 style="width:64px;height:64px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-4 border border-2 border-primary flex-shrink-0"
                                 style="width:64px;height:64px;">
                                {{ $initials }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold fs-6">{{ $karyawan->nm_lengkap ?? $karyawan->nm_karyawan ?? '-' }}</div>
                            <div class="text-muted small">NIK: {{ $karyawan->nik ?? '-' }}</div>
                            <div class="text-muted small">{{ $karyawan->jabatan->nm_jabatan ?? '-' }}</div>
                            <div class="text-muted small">{{ $karyawan->departemen->nm_departemen ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Salary breakdown -->
                    <div class="d-flex flex-wrap flex-grow-1">
                        <div class="p-4 flex-grow-1 border-end">
                            <div class="text-muted small mb-1"><i class="ti ti-cash me-1"></i>Gaji Pokok</div>
                            <div class="fw-bold fs-5 text-dark">Rp {{ number_format($gajiPokok, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 flex-grow-1 border-end">
                            <div class="text-muted small mb-1"><i class="ti ti-gift me-1"></i>Tunjangan</div>
                            <div class="fw-bold fs-5 text-dark">Rp {{ number_format($tunjangan, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 flex-grow-1 border-end" style="background:#f0fdf4;">
                            <div class="text-muted small mb-1"><i class="ti ti-wallet me-1 text-success"></i>Maks. Panjar Gaji <span class="badge bg-light-success text-success ms-1">50%</span></div>
                            <div class="fw-bold fs-5 text-success">Rp {{ number_format($maxPanjar, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-4 flex-grow-1" style="background:#eff6ff;">
                            <div class="text-muted small mb-1"><i class="ti ti-heart-handshake me-1 text-primary"></i>Maks. PKK / bln <span class="badge bg-light-primary text-primary ms-1">35%</span></div>
                            <div class="fw-bold fs-5 text-primary">Rp {{ number_format($maxPkk, 0, ',', '.') }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <form action="{{ route('pinjaman.store') }}" method="POST" enctype="multipart/form-data" id="formPinjaman">
            @csrf
            <div class="row g-4">

                <!-- Left: Loan Type & Reason -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header py-3">
                            <h5 class="card-title mb-0"><i class="ti ti-category me-2 text-primary"></i>Jenis & Alasan Pinjaman</h5>
                        </div>
                        <div class="card-body">

                            <!-- Loan type -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Jenis Pinjaman <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="kategori" id="kat1" value="1" {{ old('kategori','1')=='1'?'checked':'' }} required>
                                        <label for="kat1" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1">
                                            <i class="ti ti-wallet fs-22"></i>
                                            <span class="fw-semibold small">Panjar Gaji</span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="kategori" id="kat2" value="2" {{ old('kategori')=='2'?'checked':'' }}>
                                        <label for="kat2" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1">
                                            <i class="ti ti-heart-handshake fs-22"></i>
                                            <span class="fw-semibold small text-center">PKK</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-danger">*</span></label>
                                <select class="form-select" name="alasan_pengajuan" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    @php
                                    $alasans = [
                                        'Pendidikan',
                                        'Berobat',
                                        'Rumah Tangga',
                                        'Musibah',
                                        'Alasan darurat lainnya (pengurusan SIM & SIO)',
                                    ];
                                    @endphp
                                    @foreach($alasans as $al)
                                        <option value="{{ $al }}" {{ old('alasan_pengajuan')==$al?'selected':'' }}>{{ $al }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Info box based on type -->
                            <div id="infoPanjar" class="alert alert-info border-start border-info border-3 py-3 mt-3">
                                <div class="fw-bold mb-1"><i class="ti ti-info-circle me-1"></i>Panjar Gaji</div>
                                <div class="small">
                                    <div>Maksimal Pengajuan: <strong class="text-primary">50% dari Gaji Pokok</strong></div>
                                    <div class="mt-1">Gaji Pokok: <strong>Rp {{ number_format($gajiPokok,0,',','.') }}</strong></div>
                                    <div>Maks. Panjar: <strong class="text-success">Rp {{ number_format($maxPanjar,0,',','.') }}</strong></div>
                                </div>
                            </div>
                            <div id="infoPkk" class="alert alert-primary border-start border-primary border-3 py-3 mt-3" style="display:none;">
                                <div class="fw-bold mb-1"><i class="ti ti-info-circle me-1"></i>Pinjaman Kesejahteraan Karyawan (PKK)</div>
                                <div class="small">
                                    <div>Maks. Angsuran / bln: <strong class="text-primary">35% dari Gaji Pokok</strong></div>
                                    <div class="mt-1">Gaji Pokok: <strong>Rp {{ number_format($gajiPokok,0,',','.') }}</strong></div>
                                    <div>Maks. Angsuran: <strong class="text-success">Rp {{ number_format($maxPkk,0,',','.') }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Calculation + Docs -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header py-3">
                            <h5 class="card-title mb-0"><i class="ti ti-calculator me-2 text-primary"></i>Rincian Pengajuan</h5>
                        </div>
                        <div class="card-body">

                            <!-- Panjar fields (readonly) -->
                            <div id="fieldsPanjar">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jumlah Pengajuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="displayNominalPanjar" class="form-control bg-light fw-bold text-success"
                                               value="{{ number_format($maxPanjar,0,',','.') }}" readonly>
                                    </div>
                                    <small class="text-muted">Otomatis: 50% dari Gaji Pokok</small>
                                    <!-- Hidden actual input -->
                                    <input type="hidden" id="hiddenNominalPanjar" name="nominal_apply" value="{{ $maxPanjar }}">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tenor (Bulan)</label>
                                        <input type="number" id="tenorPanjar" name="tenor_apply_panjar" class="form-control bg-light" value="1" readonly>
                                        <input type="hidden" name="tenor_apply" id="hiddenTenorPanjar" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Angsuran / Bulan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" id="angsuranPanjar" class="form-control bg-light fw-bold text-success"
                                                   value="{{ number_format($maxPanjar,0,',','.') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PKK fields (editable) -->
                            <div id="fieldsPkk" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jumlah Pengajuan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="nominalPkk" name="nominal_apply_pkk" class="form-control"
                                               placeholder="0"
                                               value="{{ old('nominal_apply_pkk') }}">
                                    </div>
                                    <small class="text-muted">Nilai Angsuran per bulan maksimal: <strong>Rp {{ number_format($maxPkk,0,',','.') }}</strong> (35% dari Gaji Pokok)</small>
                                    <input type="hidden" id="hiddenNominalPkk" name="nominal_apply_pkk_hidden" value="">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tenor (Bulan) <span class="text-danger">*</span></label>
                                        <input type="number" id="tenorPkk" name="tenor_apply_pkk" class="form-control"
                                               min="1" max="60" placeholder="cth: 12"
                                               value="{{ old('tenor_apply_pkk') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Angsuran / Bulan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" id="angsuranPkk" class="form-control bg-light fw-bold text-success" readonly placeholder="0">
                                        </div>
                                        <div id="angsuranWarning" class="text-danger small mt-1" style="display:none;">
                                            Angsuran melebihi 35% gaji pokok!
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Upload (PKK only) -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="form-label fw-semibold mb-0"><i class="ti ti-paperclip me-1"></i>Upload Dokumen Pendukung</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddDoc">
                                            <i class="ti ti-plus me-1"></i>Tambah Dokumen
                                        </button>
                                    </div>
                                    <div id="dokumenContainer">
                                        <!-- Document rows added dynamically -->
                                    </div>
                                    <small class="text-muted">Format: PDF, JPG, PNG. Maks 5MB per file.</small>
                                </div>
                            </div>

                            <!-- Hidden fields that get set before submit -->
                            <input type="hidden" name="nominal_apply" id="finalNominal">
                            <input type="hidden" name="tenor_apply" id="finalTenor">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="ti ti-send me-2"></i>Kirim Pengajuan
                        </button>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    const maxPanjar = {{ $maxPanjar }};
    const maxPkk    = {{ $maxPkk }};
    let docCount    = 0;

    function formatRp(val) {
        return new Intl.NumberFormat('id-ID').format(Math.round(val));
    }

    // --- Switch between Panjar / PKK ---
    function switchKategori(kat) {
        if (kat === '1') {
            $('#fieldsPanjar').show();
            $('#fieldsPkk').hide();
            $('#infoPanjar').show();
            $('#infoPkk').hide();
            // set hidden panjar fields
            $('#finalNominal').val(maxPanjar);
            $('#finalTenor').val(1);
            // Remove PKK name attrs so they don't conflict
            $('[name="nominal_apply_pkk"]').removeAttr('required');
            $('[name="tenor_apply_pkk"]').removeAttr('required');
        } else {
            $('#fieldsPanjar').hide();
            $('#fieldsPkk').show();
            $('#infoPanjar').hide();
            $('#infoPkk').show();
            // Add at least one doc row if empty
            if (docCount === 0) addDocRow();
            $('[name="nominal_apply_pkk"]').attr('required', true);
            $('[name="tenor_apply_pkk"]').attr('required', true);
        }
    }

    $('input[name="kategori"]').on('change', function() {
        switchKategori($(this).val());
    });

    // Init on load
    switchKategori($('input[name="kategori"]:checked').val() || '1');

    // --- PKK calculation ---
    function calcPkk() {
        const nominalStr = $('#nominalPkk').val().replace(/\./g, '');
        const nominal = parseFloat(nominalStr) || 0;
        const tenor   = parseInt($('#tenorPkk').val()) || 0;
        const angsuran = (tenor > 0) ? nominal / tenor : 0;
        $('#angsuranPkk').val(formatRp(angsuran));

        // Warnings
        if (angsuran > maxPkk) {
            $('#angsuranWarning').show();
        } else {
            $('#angsuranWarning').hide();
        }

        // Set final hidden
        $('#finalNominal').val(nominal);
        $('#finalTenor').val(tenor);
    }

    // Format input as Rupiah on typing
    $('#nominalPkk').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            val = parseInt(val, 10).toString();
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        } else {
            $(this).val('');
        }
        calcPkk();
    });

    $('#tenorPkk').on('input', function() {
        calcPkk();
    });

    // --- Dynamic document rows ---
    function addDocRow() {
        docCount++;
        const row = `
            <div class="d-flex gap-2 mb-2 align-items-start doc-row" data-doc="${docCount}">
                <div class="flex-grow-1">
                    <input type="file" name="dokumen[]" class="form-control mb-1" accept=".pdf,.jpg,.jpeg,.png" required>
                    <input type="text" name="keterangan_dokumen[]" class="form-control form-control-sm" placeholder="Keterangan dokumen (opsional)">
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger mt-1 btn-remove-doc">
                    <i class="ti ti-trash"></i>
                </button>
            </div>`;
        $('#dokumenContainer').append(row);
    }

    $('#btnAddDoc').on('click', addDocRow);

    $(document).on('click', '.btn-remove-doc', function() {
        $(this).closest('.doc-row').remove();
        if ($('.doc-row').length === 0) docCount = 0;
    });

    // --- Form submit: validate & set hidden fields ---
    $('#formPinjaman').on('submit', function(e) {
        const kat = $('input[name="kategori"]:checked').val();
        if (kat === '1') {
            $('#finalNominal').val(maxPanjar);
            $('#finalTenor').val(1);
        } else {
            const nominalStr = $('#nominalPkk').val().replace(/\./g, '');
            const nominal = parseFloat(nominalStr) || 0;
            const tenor   = parseInt($('#tenorPkk').val()) || 0;
            if (nominal <= 0) {
                e.preventDefault();
                alert('Jumlah pengajuan tidak valid.');
                return;
            }
            if (tenor < 1) {
                e.preventDefault();
                alert('Tenor minimal 1 bulan.');
                return;
            }
            const angsuran = nominal / tenor;
            if (angsuran > maxPkk) {
                e.preventDefault();
                alert('Angsuran per bulan melebihi batas 35% gaji pokok.');
                return;
            }
            $('#finalNominal').val(nominal);
            $('#finalTenor').val(tenor);
        }
    });
});
</script>
@endsection
