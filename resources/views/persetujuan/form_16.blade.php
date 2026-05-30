<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-clipboard-text me-2"></i>Form Persetujuan Pengajuan Exit Interviews
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('approval.store') }}" method="post" id="myForm">
{{ csrf_field() }}
<input type="hidden" name="id_pengajuan" value="{{ $data_approval->id }}">
<input type="hidden" name="key_approval" value="{{ $data_approval->approval_key }}">
<input type="hidden" name="level_approval" value="{{ $data_approval->approval_level }}">
<input type="hidden" name="date_approval" value="{{ $data_approval->approval_date }}">
<input type="hidden" name="group_approval" value="{{ $data_approval->approval_group }}">
<input type="hidden" name="status_approval" value="{{ $profil->status_pengajuan }}">
<div class="modal-body p-4">

    <div class="row g-3 mb-3">
        <div class="col-md-7">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-user me-2"></i>Profil Karyawan
                    </h6>
                    <div class="d-flex align-items-center mb-3">
                        @if(!empty($profil->getPengajuan->getKaryawan->photo))
                            <a href="{{ url(Storage::url('hrd/photo/'.$profil->getPengajuan->getKaryawan->photo)) }}" data-fancybox data-caption="avatar">
                            <img src="{{ url(Storage::url('hrd/photo/'.$profil->getPengajuan->getKaryawan->photo)) }}" class="rounded-circle me-3" alt="avatar" style="width: 70px; height: 70px; object-fit: cover;"></a>
                        @else
                            <a href="{{ asset('assets/images/user/1.jpg') }}" data-fancybox data-caption="avatar">
                            <img src="{{ asset('assets/images/user/1.jpg') }}" class="rounded-circle me-3" alt="avatar" style="width: 70px; height: 70px; object-fit: cover;"></a>
                        @endif
                        <div>
                            <div class="text-muted small">{{ $profil->getPengajuan->getKaryawan->nik }}</div>
                            <div class="fw-bold">{{ $profil->getPengajuan->getKaryawan->nm_lengkap }}</div>
                            <div class="text-muted small">{{ $profil->getPengajuan->getKaryawan->get_jabatan->nm_jabatan }} | {{ $profil->getPengajuan->getKaryawan->get_departemen->nm_dept }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-info-circle me-2"></i>Data Pengajuan Resign
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Tanggal Pengajuan</td>
                            <td>
                                <span class="badge bg-light-primary text-primary">{{ date('d-m-Y', strtotime($profil->getPengajuan->created_at)) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alasan Pengajuan</td>
                            <td class="fw-semibold">{{ $profil->getPengajuan->alasan_resign }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Rencana Efektif Resign</td>
                            <td>
                                <span class="badge bg-light-danger text-danger">{{ date_format(date_create($profil->getPengajuan->tgl_eff_resign), 'd-m-Y') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-hierarchy-2 me-2"></i>Hirarki Persetujuan
                    </h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th rowspan="2" class="text-center" style="width: 5%">Lvl</th>
                                    <th rowspan="2">Pejabat</th>
                                    <th class="text-center">Tgl</th>
                                    <th class="text-center">Status</th>
                                </tr>
                                <tr></tr>
                            </thead>
                            <tbody>
                                @foreach ($hirarki_persetujuan as $list)
                                <tr>
                                    <td class="text-center">
                                        @if($list->approval_active==1)
                                        <span class="badge rounded-pill bg-success">{{ $list->approval_level }}</span>
                                        @else
                                        <span class="badge rounded-pill bg-secondary">{{ $list->approval_level }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">{{ $list->get_profil_employee->nm_lengkap }}</div>
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $list->get_profil_employee->get_jabatan->nm_jabatan }}</div>
                                    </td>
                                    <td class="text-center small">{{ (empty($list->approval_date)) ? "-" : date('d-m-Y', strtotime($list->approval_date))  }}</td>
                                    <td class="text-center">
                                        @if($list->approval_status==1)
                                        <span class="badge bg-light-success text-success">Approved</span>
                                        @elseif($list->approval_status==2)
                                        <span class="badge bg-light-danger text-danger">Rejected</span>
                                        @else
                                        <span class="badge bg-light-warning text-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-edit me-2"></i>Form Persetujuan
                    </h6>
                    <div class="row align-items-center mb-3">
                        <label class="col-sm-4 col-form-label text-muted">Status</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="pil_persetujuan" name="pil_persetujuan" style="width: 100%;" required>
                                <option value="1">Setuju</option>
                                <option value="2">Tolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label class="text-muted mb-1">Deskripsi Persetujuan</label>
                            <textarea class="form-control" name="inp_keterangan" id="inp_keterangan" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-light border-0">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3">
                <i class="ti ti-list-details me-2"></i>Form Exit Interviews
            </h6>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">1. Apa yang menjadi alasan anda mengundurkan diri dari PT. SSB?</h6>
                    <textarea class="form-control mb-3" name="inp_jawaban_1" id="inp_jawaban_1" rows="2" disabled>{{ $profil->jawaban_1 }}</textarea>
                    <p class="text-muted small mb-2">* Jika pindah ke perusahaan lain, silahkan isi informasi berikut:</p>
                    <div class="row mb-2">
                        <label class="col-lg-4 text-muted small">Nama Perusahaan</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm" name="inp_jawaban_1_1" id="inp_jawaban_1_1" value="{{ $profil->jawaban_1_1 }}" disabled>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-lg-4 text-muted small">Posisi yang ditawarkan</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm" name="inp_jawaban_1_2" id="inp_jawaban_1_2" value="{{ $profil->jawaban_1_2 }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4 text-muted small">Gaji yang ditawarkan</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm angka" name="inp_jawaban_1_3" id="inp_jawaban_1_3" value="{{ $profil->jawaban_1_3 }}" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">2. Apa yang membuat anda mempertimbangkan keputusan anda untuk mengundurkan diri?</h6>
                    <textarea class="form-control" name="inp_jawaban_2" id="inp_jawaban_2" rows="2" disabled>{{ $profil->jawaban_2 }}</textarea>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">3. Adakah hal-hal yang tidak sesuai dengan keinginan anda selama anda bekerja di PT SSB?</h6>
                    <textarea class="form-control" name="inp_jawaban_3" id="inp_jawaban_3" rows="2" disabled>{{ $profil->jawaban_3 }}</textarea>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">4. Apakah menurut anda upah yang diterima dari perusahaan ini sesuai dengan kemampuan dan posisi anda?</h6>
                    <textarea class="form-control" name="inp_jawaban_4" id="inp_jawaban_4" rows="2" disabled>{{ $profil->jawaban_4 }}</textarea>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">5. Apakah menurut anda fasilitas yang diberikan oleh perusahaan kepada karyawan sudah cukup? Kalau tidak, silahkan berikan alasan.</h6>
                    <textarea class="form-control" name="inp_jawaban_5" id="inp_jawaban_5" rows="2" disabled>{{ $profil->jawaban_5 }}</textarea>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">6. Apa yang paling anda sukai dari pekerjaan anda di PT SSB?</h6>
                    <textarea class="form-control mb-3" name="inp_jawaban_6" id="inp_jawaban_6" rows="2" disabled>{{ $profil->jawaban_6 }}</textarea>
                    <p class="text-muted small mb-2">* Berikut ringkasan karir anda di PT SSB:</p>
                    <div class="row mb-2">
                        <label class="col-lg-4 text-muted small">Posisi awal bergabung</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm" name="inp_jawaban_6_1" id="inp_jawaban_6_1" value="{{ $profil->jawaban_6_1 }}" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4 text-muted small">Posisi terakhir</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control form-control-sm" name="inp_jawaban_6_2" id="inp_jawaban_6_2" value="{{ $profil->jawaban_6_2 }}" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">7. Menurut anda, apakah tugas & tanggung jawab pekerjaan anda sudah jelas selama di PT SSB?</h6>
                    <textarea class="form-control" name="inp_jawaban_7" id="inp_jawaban_7" rows="2" disabled>{{ $profil->jawaban_7 }}</textarea>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">8. Bagaimana anda menilai atasan langsung anda?</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="checkBagus" name="pilihan_8" value="Bagus" {{ ($profil->jawaban_8=="Bagus") ? "checked" : "" }} disabled>
                            <label class="form-check-label" for="checkBagus">Bagus</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="checkCukup" name="pilihan_8" value="Cukup" {{ ($profil->jawaban_8=="Cukup") ? "checked" : "" }} disabled>
                            <label class="form-check-label" for="checkCukup">Cukup</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="checkKurang" name="pilihan_8" value="Kurang" {{ ($profil->jawaban_8=="Kurang") ? "checked" : "" }} disabled>
                            <label class="form-check-label" for="checkKurang">Kurang</label>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-2 text-muted small">Alasan</label>
                        <div class="col-lg-10">
                            <textarea class="form-control" name="inp_jawaban_8_1" id="inp_jawaban_8_1" rows="2" disabled>{{ $profil->jawaban_8_1 }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">9. Menurut anda perbaikan apakah yang harus dilakukan oleh manajemen di PT. SSB?</h6>
                    <textarea class="form-control mb-3" name="inp_jawaban_9" id="inp_jawaban_9" rows="2" disabled>{{ $profil->jawaban_9 }}</textarea>
                    <p class="text-muted small mb-2">* Berikan skala penilaian 1 s/d 4 (4 = paling baik)</p>
                    @php
                    $rating_items = [
                        ['key'=>'pilihan_9_1', 'val'=>$profil->jawaban_9_1, 'label'=>'1. Kenyamanan kerja'],
                        ['key'=>'pilihan_9_2', 'val'=>$profil->jawaban_9_2, 'label'=>'2. Beban kerja'],
                        ['key'=>'pilihan_9_3', 'val'=>$profil->jawaban_9_3, 'label'=>'3. Gaji & Tunjangan'],
                        ['key'=>'pilihan_9_4', 'val'=>$profil->jawaban_9_4, 'label'=>'4. Kesempatan berkembang'],
                        ['key'=>'pilihan_9_5', 'val'=>$profil->jawaban_9_5, 'label'=>'5. Efektivitas organisasi'],
                        ['key'=>'pilihan_9_6', 'val'=>$profil->jawaban_9_6, 'label'=>'6. Kelebihan Asuransi'],
                        ['key'=>'pilihan_9_7', 'val'=>$profil->jawaban_9_7, 'label'=>'7. Perhatian Manajemen'],
                        ['key'=>'pilihan_9_8', 'val'=>$profil->jawaban_9_8, 'label'=>'8. Lingkungan Kerja'],
                        ['key'=>'pilihan_9_9', 'val'=>$profil->jawaban_9_9, 'label'=>'9. Kualitas pelatihan'],
                    ];
                    @endphp
                    @foreach ($rating_items as $idx => $item)
                    <div class="row align-items-center mb-2">
                        <label class="col-lg-5 text-muted small">{{ $item['label'] }}</label>
                        <div class="col-lg-7">
                            @for($s=1; $s<=4; $s++)
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="check_{{ $idx }}_{{ $s }}" name="{{ $item['key'] }}" value="{{ $s }}" {{ ($item['val']==$s) ? "checked" : "" }} disabled>
                                <label class="form-check-label small" for="check_{{ $idx }}_{{ $s }}">{{ $s }}</label>
                            </div>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary">10. Berikan komentar anda selain yang sudah dituliskan diatas sebagai masukan perusahaan.</h6>
                    <textarea class="form-control" name="inp_jawaban_10" id="inp_jawaban_10" rows="3" disabled>{{ $profil->jawaban_10 }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer border-0">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
    <button type="submit" class="btn btn-primary">
        <i class="ti ti-device-floppy me-1"></i>Simpan Persetujuan
    </button>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function()
    {
        $(".select2").select2({
            dropdownParent: $('#modalFormPersetujuan')
        });
        window.setTimeout(function () { $("#success-alert").alert('close'); }, 2000);
    });
    document.querySelector('#myForm').addEventListener('submit', function(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Yakin data akan disimpan?',
            text: "Submit pengajuan persetujuan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
