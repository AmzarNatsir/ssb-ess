<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-users-plus me-2"></i>Form Persetujuan Pengajuan Tenaga Kerja
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('approval.store') }}" method="post" onsubmit="return konfirm()">
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
                        <i class="ti ti-info-circle me-2"></i>Data Pengajuan
                    </h6>
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted" style="width:45%">Departemen/Divisi</td>
                            <td class="fw-semibold">{{ $profil->get_departemen->nm_dept }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Posisi/Jabatan</td>
                            <td class="fw-semibold">{{ $profil->get_jabatan->nm_jabatan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah Orang</td>
                            <td class="fw-semibold text-primary">{{ $profil->jumlah_orang }} orang</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Dibutuhkan</td>
                            <td class="fw-semibold">{{ date('d-m-Y', strtotime($profil->tanggal_dibutuhkan)) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alasan Permintaan</td>
                            <td class="fw-semibold">{{ $profil->alasan_permintaan }}</td>
                        </tr>
                    </table>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-list-check me-2"></i>Kualifikasi yang Dibutuhkan
                    </h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:45%">Jenis Kelamin</td>
                            <td class="fw-semibold">
                                @if ($profil->jenkel == 1) Laki-Laki
                                @elseif($profil->jenkel == 2) Perempuan
                                @else Laki-Laki / Perempuan @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Umur</td>
                            <td class="fw-semibold">{{ $profil->umur_min }} s/d {{ $profil->umur_maks }} Tahun</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pendidikan</td>
                            <td class="fw-semibold">{{ $profil->pendidikan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Keahlian Khusus</td>
                            <td class="fw-semibold">{{ $profil->keahlian_khusus }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pengalaman</td>
                            <td class="fw-semibold">{{ $profil->pengalaman }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bahasa Inggris</td>
                            <td class="fw-semibold">{{ $profil->kemampuan_bahasa_ing }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bahasa Indonesia</td>
                            <td class="fw-semibold">{{ $profil->kemampuan_bahasa_ind }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bahasa Lain</td>
                            <td class="fw-semibold">{{ $profil->kemampuan_bahasa_lain }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kepribadian</td>
                            <td class="fw-semibold">{{ $profil->kepribadian }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Catatan</td>
                            <td class="fw-semibold">{{ $profil->catatan }}</td>
                        </tr>
                    </table>
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
    function konfirm()
    {
        var psn = confirm("Yakin data akan disimpan ?");
        if(psn==true)
        {
            return true;
        } else {
            return false;
        }
    }
</script>
