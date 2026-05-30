<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-clock me-2"></i>Form Persetujuan Pengajuan Lembur
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
                        <i class="ti ti-info-circle me-2"></i>Data Pengajuan Lembur
                    </h6>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label class="text-muted small mb-1">Tanggal</label>
                            <input type="text" name="tanggal" id="tanggal" class="form-control" value="{{ date('d-m-Y', strtotime($profil->tgl_pengajuan)) }}" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <label class="text-muted small mb-1">Mulai Jam</label>
                            <input type="text" name="jam_mulai" id="jam_mulai" class="form-control jamClass" placeholder="00:00" style="text-align: center" value="{{ date('H:s', strtotime($profil->jam_mulai)) }}" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label class="text-muted small mb-1">Selesai Jam</label>
                            <input type="text" name="jam_selesai" id="jam_selesai" class="form-control jamClass" placeholder="00:00" onblur="getTotal(this)" style="text-align: center" value="{{ date('H:s', strtotime($profil->jam_selesai)) }}" disabled>
                        </div>
                        <div class="col-sm-4">
                            <label class="text-muted small mb-1">Total Jam</label>
                            <input type="text" name="inp_total" id="inp_total" class="form-control fw-bold text-primary" value="{{ $profil->total_jam }} Jam" style="text-align: center" disabled>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Deskripsi Pekerjaan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3" disabled>{{ $profil->deskripsi_pekerjaan }}</textarea>
                    </div>
                    <div>
                        <label class="text-muted small mb-2">Lampiran Surat Perintah Lembur</label>
                        <div class="text-center">
                            @if(!empty($profil->file_surat_perintah_lembur))
                            <a href="{{ asset('storage/' . $profil->file_surat_perintah_lembur) }}" data-fancybox data-caption="Surat Perintah Lembur">
                                <img src="{{ asset('storage/' . $profil->file_surat_perintah_lembur) }}" alt="Surat Perintah Lembur" class="rounded border" style="max-width: 100%; max-height: 200px;">
                            </a>
                            @else
                            <a href="{{ asset('assets/images/user/1.jpg') }}" data-fancybox data-caption="default">
                                <img src="{{ asset('assets/images/user/1.jpg') }}" alt="default" class="rounded border" style="max-width: 100%; max-height: 200px;">
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
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

