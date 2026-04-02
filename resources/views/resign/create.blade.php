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
                        <li class="breadcrumb-item"><a href="{{ route('resign.index') }}">Pengajuan Resign</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat Pengajuan</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('resign.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-x me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-info-circle fs-2 me-3 text-info"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Informasi Penting</h6>
                        <small>Pengajuan pengunduran diri dibuat 30 hari sebelum pengunduran diri Anda. Sistem secara otomatis menetapkan tanggal efektif resign Anda 30 hari sejak formulir ini diajukan.</small>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title fw-bold mb-0 text-primary">Form Pengunduran Diri (Resign)</h5>
                    </div>
                    <form action="{{ route('resign.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tanggal Efektif Resign <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="ti ti-calendar-event"></i></span>
                                    <input type="date" class="form-control fw-bold bg-light" name="tgl_eff_resign" value="{{ $tgl_eff_resign }}" readonly required>
                                </div>
                                <small class="text-muted mt-1 d-block">+30 hari dari tanggal pengajuan (Otomatis)</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alasan_resign" rows="4" placeholder="Tuliskan alasan pengunduran diri Anda..." required>{{ old('alasan_resign') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Pernyataan Pengunduran Diri (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="file_surat_resign" accept=".pdf" required>
                                <small class="text-muted mt-1 d-block">Format file harus .pdf dengan ukuran maksimal 2MB.</small>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top text-end py-3">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="ti ti-device-floppy me-2"></i>Simpan Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
