@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Exit Interview Form</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resign.index') }}">Pengajuan Resign</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Isi Exit Interview</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('resign.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-md-10 mx-auto">
                
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

                <div class="card shadow-sm border-top border-primary border-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title fw-bold mb-0 text-primary">Silahkan isi jawaban pertanyaan berikut:</h5>
                    </div>
                    <form action="{{ route('exit-interview.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_head" value="{{ $resign->id }}">
                        
                        <div class="card-body">
                            <!-- Pertanyaan 1 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">1. Apa yang menjadi alasan anda mengundurkan diri dari PT. SSB <span class="text-danger">*</span></label>
                                <textarea class="form-control mb-3" name="jawaban_1" rows="3" required>{{ old('jawaban_1') }}</textarea>
                                
                                <div class="bg-light p-3 rounded border">
                                    <small class="text-primary d-block mb-3">* Jika pindah ke perusahaan lain, silakan sebut informasi berikut:</small>
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-3"><label class="col-form-label small text-muted">Nama Perusahaan :</label></div>
                                        <div class="col-sm-9"><input type="text" class="form-control form-control-sm" name="jawaban_1_1" value="{{ old('jawaban_1_1') }}"></div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-3"><label class="col-form-label small text-muted">Posisi yang ditawarkan :</label></div>
                                        <div class="col-sm-9"><input type="text" class="form-control form-control-sm" name="jawaban_1_2" value="{{ old('jawaban_1_2') }}"></div>
                                    </div>
                                    <div class="row align-items-center mb-0">
                                        <div class="col-sm-3"><label class="col-form-label small text-muted">Gaji yang ditawarkan :</label></div>
                                        <div class="col-sm-9">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" name="jawaban_1_3" id="jawaban_1_3" value="{{ old('jawaban_1_3') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pertanyaan 2 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">2. Apa yang membuat anda mempertimbangkan keputusan anda untuk mengundurkan diri? <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_2" rows="3" required>{{ old('jawaban_2') }}</textarea>
                            </div>

                            <!-- Pertanyaan 3 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">3. Adakah hal-hal yang tidak sesuai dengan keinginan anda selama anda bekerja di PT SSB? <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_3" rows="3" required>{{ old('jawaban_3') }}</textarea>
                            </div>

                            <!-- Pertanyaan 4 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">4. Apakah menurut anda upah yang diterima dari perusahaan ini sesuai dengan kemampuan dan posisi anda? <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_4" rows="3" required>{{ old('jawaban_4') }}</textarea>
                            </div>

                            <!-- Pertanyaan 5 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">5. Apakah menurut anda fasilitas yang diberikan oleh perusahaan kepada karyawan sudah cukup? Kalau tidak, silakan berikan alasan. <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_5" rows="3" required>{{ old('jawaban_5') }}</textarea>
                            </div>

                            <!-- Pertanyaan 6 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">6. Apa yang paling anda sukai dari pekerjaan anda di PT SSB? <span class="text-danger">*</span></label>
                                <textarea class="form-control mb-3" name="jawaban_6" rows="3" required>{{ old('jawaban_6') }}</textarea>

                                <div class="bg-light p-3 rounded border">
                                    <small class="text-primary d-block mb-3">* Berikut ringkasan karir anda di PT SSB, silahkan lengkapi informasi berikut:</small>
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-3"><label class="col-form-label small text-muted">Posisi awal bergabung :</label></div>
                                        <div class="col-sm-4"><input type="text" class="form-control form-control-sm" name="jawaban_6_1" value="{{ old('jawaban_6_1', $resign->karyawan->jabatan_awal->nm_jabatan ?? '-') }}" required></div>
                                    </div>
                                    <div class="row align-items-center mb-0">
                                        <div class="col-sm-3"><label class="col-form-label small text-muted">Posisi terakhir :</label></div>
                                        <div class="col-sm-4"><input type="text" class="form-control form-control-sm" name="jawaban_6_2" value="{{ old('jawaban_6_2', $resign->karyawan->jabatan->nm_jabatan ?? '-') }}" readonly></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pertanyaan 7 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">7. Menurut anda, apakah tugas & tanggung jawab pekerjaan anda sudah jelas selama di PT SSB? <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_7" rows="3" required>{{ old('jawaban_7') }}</textarea>
                            </div>

                            <!-- Pertanyaan 8 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary d-block">8. Bagaimana anda menilai atasan langsung anda? <span class="text-danger">*</span></label>
                                <div class="d-flex gap-4 mb-3 ps-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban_8" id="j8_1" value="Bagus" {{ old('jawaban_8') == 'Bagus' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="j8_1">Bagus</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban_8" id="j8_2" value="Cukup" {{ old('jawaban_8') == 'Cukup' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="j8_2">Cukup</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban_8" id="j8_3" value="Kurang" {{ old('jawaban_8') == 'Kurang' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="j8_3">Kurang</label>
                                    </div>
                                </div>
                                
                                <label class="form-label fw-semibold small text-muted">Alasan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_8_1" rows="2" required>{{ old('jawaban_8_1') }}</textarea>
                            </div>

                            <!-- Pertanyaan 9 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">9. Menurut anda perbaikan apakah yang harus dilakukan oleh manajemen di PT. SSB agar perusahaan semakin baik, dilihat dari posisi anda sebagai karyawan? <span class="text-danger">*</span></label>
                                <textarea class="form-control mb-3" name="jawaban_9" rows="3" required>{{ old('jawaban_9') }}</textarea>

                                <div class="bg-light p-3 rounded border">
                                    <small class="text-primary d-block mb-3">* Mohon berikan skala penilaian (1 s/d 4) di bawah ini (1 = Kurang Sekali, 2 = Kurang, 3 = Baik, 4 = Sangat Baik)</small>
                                    
                                    @php
                                        $ratings = [
                                            ['name' => 'jawaban_9_1', 'label' => '1. Kenyamanan Kerja'],
                                            ['name' => 'jawaban_9_2', 'label' => '2. Sistem Kerja'],
                                            ['name' => 'jawaban_9_3', 'label' => '3. Gaji & Tunjangan'],
                                            ['name' => 'jawaban_9_4', 'label' => '4. Kesempatan Berkembang'],
                                            ['name' => 'jawaban_9_5', 'label' => '5. Efektivitas Organisasi'],
                                            ['name' => 'jawaban_9_6', 'label' => '6. Kebijakan Asuransi'],
                                            ['name' => 'jawaban_9_7', 'label' => '7. Perhatian Manajemen'],
                                            ['name' => 'jawaban_9_8', 'label' => '8. Lingkungan Kerja'],
                                            ['name' => 'jawaban_9_9', 'label' => '9. Fasilitas Penunjang'],
                                        ];
                                    @endphp

                                    @foreach($ratings as $rating)
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-4"><label class="col-form-label small text-muted">{{ $rating['label'] }}</label></div>
                                        <div class="col-sm-8 d-flex gap-3 align-items-center">
                                            @for($i=1; $i<=4; $i++)
                                            <div class="form-check form-check-inline mx-0 my-0">
                                                <input class="form-check-input mt-0" type="radio" name="{{ $rating['name'] }}" id="{{ $rating['name'] }}_{{ $i }}" value="{{ $i }}" {{ old($rating['name']) == $i ? 'checked' : '' }} required>
                                                <label class="form-check-label pt-0 mt-0" for="{{ $rating['name'] }}_{{ $i }}">{{ $i }}</label>
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="mt-2 text-center text-white p-1" style="background-color: #f7504f; font-size: 0.85rem; border-radius: 4px;">
                                        1 = Sangat Kurang | 2 = Kurang | 3 = Baik | 4 = Sangat Baik
                                    </div>
                                </div>
                            </div>

                            <!-- Pertanyaan 10 -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-primary">10. Berikan komentar anda selain yang sudah dituliskan diatas sebagai masukan perusahaan. <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="jawaban_10" rows="3" required>{{ old('jawaban_10') }}</textarea>
                            </div>

                        </div>
                        <div class="card-footer bg-white border-top text-end py-3">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-4">
                                <i class="ti ti-device-floppy me-2"></i>Kirim Exit Interview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#jawaban_1_3').on('input', function() {
        // Hanya membolehkan angka
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            // Hilangkan 0 di depan
            val = parseInt(val, 10).toString();
            // Format angka dengan titik sebagai pemisah ribuan
            $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        } else {
            $(this).val('');
        }
    });

    // Format initial value if any (e.g. on validation error back)
    let initVal = $('#jawaban_1_3').val().replace(/[^0-9]/g, '');
    if (initVal) {
        initVal = parseInt(initVal, 10).toString();
        $('#jawaban_1_3').val(initVal.replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    }
});
</script>
@endsection
