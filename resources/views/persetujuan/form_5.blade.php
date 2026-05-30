<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-user-edit me-2"></i>Form Persetujuan Pengajuan Perubahan Status Karyawan
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
        <div class="col-md-6">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-user me-2"></i>Profil Karyawan
                    </h6>
                    <div class="d-flex align-items-center mb-3">
                        @if(!empty($profil->get_profil->photo))
                            <img src="{{ url(Storage::url('hrd/photo/'.$profil->get_profil->photo)) }}" class="rounded-circle me-3" alt="avatar" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <a href="{{ asset('assets/images/user/1.jpg') }}" data-fancybox data-caption="avatar">
                            <img src="{{ asset('assets/images/user/1.jpg') }}" class="rounded-circle me-3" alt="avatar" style="width: 80px; height: 80px; object-fit: cover;"></a>
                        @endif
                        <div>
                            <div class="text-muted small">{{ $profil->get_profil->nik }}</div>
                            <div class="fw-bold">{{ $profil->get_profil->nm_lengkap }}</div>
                            <div class="text-muted small">{{ $profil->get_profil->get_jabatan->nm_jabatan }} | {{ $profil->get_profil->get_departemen->nm_dept }}</div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary mb-3">
                        <i class="ti ti-history me-2"></i>Status Karyawan Saat Ini
                    </h6>
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted" style="width:40%">Status Karyawan</td>
                            <td class="fw-semibold" id="sts_lama">{{ $profil->get_profil->get_status_karyawan($profil->get_profil->id_status_karyawan) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Efektif</td>
                            <td class="fw-semibold" id="tgl_eff_lama">{{ (empty($profil->get_profil->tgl_sts_efektif_mulai)) ? "-" : date_format(date_create($profil->get_profil->tgl_sts_efektif_mulai), 'd-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Berakhir</td>
                            <td class="fw-semibold" id="tgl_akh_lama">{{ (empty($profil->get_profil->tgl_sts_efektif_akhir)) ? "-" : date_format(date_create($profil->get_profil->tgl_sts_efektif_akhir), 'd-m-Y') }}</td>
                        </tr>
                    </table>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-info-circle me-2"></i>Data Pengajuan
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Tanggal Pengajuan</td>
                            <td class="fw-semibold">{{ date_format(date_create($profil->tgl_pengajuan), 'd-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alasan Pengajuan</td>
                            <td class="fw-semibold">{{ $profil->alasan_pengajuan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Yang Diusulkan</td>
                            <td class="fw-semibold text-primary">{{ $profil->get_status_karyawan($profil->id_sts_baru) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diajukan Oleh</td>
                            <td class="fw-semibold">
                                {{ $profil->get_diajukan_oleh->karyawan->nm_lengkap }}<br>
                                <span class="text-muted small">{{ $profil->get_diajukan_oleh->karyawan->get_jabatan->nm_jabatan }} - {{ $profil->get_diajukan_oleh->karyawan->get_departemen->nm_dept }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-paperclip me-2"></i>Lampiran Hasil Evaluasi
                    </h6>
                    <div class="mb-3">
                        <a href="{{ url('hrd/perubahanstatus/download/hasil_evaluasi/'.$profil->id) }}" target="_new" class="btn btn-outline-danger w-100">
                            <i class="ti ti-download me-1"></i>Download File Hasil Evaluasi Kerja
                        </a>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-hierarchy-2 me-2"></i>Hirarki Persetujuan
                    </h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th rowspan="2" class="text-center" style="width: 5%">Level</th>
                                    <th rowspan="2">Pejabat</th>
                                    <th colspan="3" class="text-center">Persetujuan</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Status</th>
                                </tr>
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
                                        <div class="fw-semibold">{{ $list->get_profil_employee->nm_lengkap }}</div>
                                        <div class="text-muted small">{{ $list->get_profil_employee->get_jabatan->nm_jabatan }}</div>
                                    </td>
                                    <td class="text-center">
                                        {{ (empty($list->approval_date)) ? "-" : date('d-m-Y', strtotime($list->approval_date))  }}
                                    </td>
                                    <td>{{ $list->approval_remark }}</td>
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
                        <label class="col-sm-4 col-form-label text-muted">Status Persetujuan</label>
                        <div class="col-sm-8">
                            <select class="form-control select2" id="pil_persetujuan" name="pil_persetujuan" style="width: 100%;" required>
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
</script>



