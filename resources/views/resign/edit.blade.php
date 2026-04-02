@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Edit Pengajuan Resign</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resign.index') }}">Pengajuan Resign</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Pengajuan</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('resign.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm border-top border-warning border-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">Data Pengajuan</h5>
                    </div>
                    <form action="{{ route('resign.update', $resign->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tanggal Efektif Resign</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="ti ti-calendar-event"></i></span>
                                    <input type="text" class="form-control fw-bold bg-light" value="{{ \Carbon\Carbon::parse($resign->tgl_eff_resign)->format('d F Y') }}" readonly>
                                </div>
                                <small class="text-muted mt-1 d-block">+30 hari dari tanggal {{ $resign->created_at->format('d M Y') }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alasan_resign" rows="4" placeholder="Alasan Resign" required>{{ old('alasan_resign', $resign->alasan_resign) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Dokumen Saat Ini</label>
                                <div>
                                    @if($resign->file_surat_resign)
                                        <a href="{{ asset($resign->file_surat_resign) }}" target="_blank" class="btn btn-outline-danger btn-sm mb-2"><i class="ti ti-file-type-pdf me-2"></i>Lihat Surat Resign (Tersimpan)</a>
                                    @else
                                        <span class="text-muted d-block mb-2">Belum ada file terlampir.</span>
                                    @endif
                                </div>
                                <label class="form-label mt-2">Ganti Dokumen? (Opsional)</label>
                                <input type="file" class="form-control" name="file_surat_resign" accept=".pdf">
                                <small class="text-muted mt-1 d-block">Biarkan kosong jika tidak ingin mengubah dokumen (Maks 2MB, PDF).</small>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top text-end py-3">
                            <button type="submit" class="btn btn-warning d-inline-flex align-items-center text-dark">
                                <i class="ti ti-device-floppy me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
