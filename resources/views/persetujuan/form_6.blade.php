<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-arrows-right-left me-2"></i>Form Persetujuan Pengajuan Mutasi
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
                        <i class="ti ti-user me-2"></i>Profil Karyawan
                    </h6>
                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <td class="text-muted" style="width:40%">NIK</td>
                            <td class="fw-semibold">{{ $profil->get_profil->nik }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Karyawan</td>
                            <td class="fw-semibold">{{ $profil->get_profil->nm_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Karyawan</td>
                            <td class="fw-semibold">{{ $profil->get_profil->get_status_karyawan($profil->get_profil->id_status_karyawan) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Divisi</td>
                            <td class="fw-semibold">{{ (empty($profil->get_profil->id_divisi)) ? "-" : $profil->get_profil->get_divisi->nm_divisi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Departemen</td>
                            <td class="fw-semibold">{{ (empty($profil->get_profil->id_departemen)) ? "-" : $profil->get_profil->get_departemen->nm_dept }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jabatan</td>
                            <td class="fw-semibold">{{ (empty($profil->get_profil->id_jabatan)) ? "-" :  $profil->get_profil->get_jabatan->nm_jabatan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Efektif Jabatan</td>
                            <td class="fw-semibold">{{ $ket_efektif_jabatan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Bergabung</td>
                            <td class="fw-semibold">{{ $ket_tgl_masuk }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lama Bekerja</td>
                            <td class="fw-semibold">{{ $ket_lama_kerja }}</td>
                        </tr>
                    </table>

                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-arrow-bar-to-right me-2"></i>Data Pengajuan Mutasi
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
                            <td class="text-muted">Diusulkan</td>
                            <td class="fw-semibold text-primary">
                                @foreach($list_kategori as $key => $value)
                                @if($key==$profil->kategori)
                                    {{ $value }}
                                    @php break; @endphp
                                @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Divisi Baru</td>
                            <td class="fw-semibold">{{ (empty($profil->id_divisi_br)) ? "-" : $profil->get_divisi_baru->nm_divisi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Departemen Baru</td>
                            <td class="fw-semibold">{{ (empty($profil->id_dept_br)) ? "-" : $profil->get_dept_baru->nm_dept }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sub Departemen Baru</td>
                            <td class="fw-semibold">{{ (empty($profil->id_subdept_br)) ? "-" : $profil->get_subdept_baru->nm_subdept }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jabatan Baru</td>
                            <td class="fw-semibold">{{ (empty($profil->id_jabt_br)) ? "-" :  $profil->get_jabatan_baru->nm_jabatan }}</td>
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
