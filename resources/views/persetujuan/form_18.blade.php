<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-gift me-2"></i>Form Persetujuan Tunjangan Hari Raya (THR)
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
<input type="hidden" name="periode_bulan" id="periode_bulan" value="{{ $profil->bulan }}">
<input type="hidden" name="periode_tahun" id="periode_tahun" value="{{ $profil->tahun }}">
<input type="hidden" name="departemen" id="departemen" value="">
<div class="modal-body p-4">

    <div class="card bg-light border-0 mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <i class="ti ti-calendar text-success me-2"></i>
                <h6 class="fw-bold text-primary mb-0">Daftar THR - Periode {{ \App\Helpers\Hrdhelper::get_nama_bulan($profil->bulan) }} {{ $profil->tahun }}</h6>
            </div>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border mb-2">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fw-semibold text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <i class="ti ti-users me-2"></i>Non Departemen
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered mb-0" style="font-size: 13px">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 3%">No</th>
                                            <th>Karyawan</th>
                                            <th>Posisi</th>
                                            <th class="text-center" style="width: 12%">Status Karyawan</th>
                                            <th class="text-end" style="width: 12%">Gaji Pokok</th>
                                            <th class="text-end" style="width: 12%">Tunjangan Tetap</th>
                                            <th class="text-end" style="width: 12%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $nom_non_dept = 1; @endphp
                                        @foreach ($data_non_dept as $r1)
                                        @php
                                        $gapok = $r1['list_data']['gaji_pokok'] ?? 0;
                                        $tunj_tetap = $r1['list_data']['tunj_tetap'] ?? 0;
                                        $total = $gapok + $tunj_tetap;
                                        $ket_status = '';
                                        foreach($list_status as $key => $value) {
                                            if($key==$r1['id_status_karyawan']) { $ket_status = $value; break; }
                                        }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $nom_non_dept++ }}</td>
                                            <td>{{ $r1['nik'] }} - {{ $r1['nm_lengkap'] }}</td>
                                            <td class="fw-semibold">{{ $r1['get_jabatan']['nm_jabatan'] }}</td>
                                            <td class="text-center"><span class="badge bg-light-info text-info">{{ $ket_status }}</span></td>
                                            <td class="text-end">{{ number_format($r1['list_data']['gaji_pokok'], 0) }}</td>
                                            <td class="text-end">{{ number_format($tunj_tetap, 0) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($total, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach ($data_dept as $r)
                <div class="accordion-item border mb-2">
                    <h2 class="accordion-header" id="heading{{ $r['id'] }}">
                        <button class="accordion-button collapsed fw-semibold text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $r['id'] }}" aria-expanded="false" aria-controls="collapse{{ $r['id'] }}">
                            <i class="ti ti-building me-2"></i>{{ $r['nm_dept'] }}
                        </button>
                    </h2>
                    <div id="collapse{{ $r['id'] }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $r['id'] }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered mb-0" style="font-size: 13px">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" style="width: 3%">No</th>
                                            <th>Karyawan</th>
                                            <th>Posisi</th>
                                            <th class="text-center" style="width: 12%">Status Karyawan</th>
                                            <th class="text-end" style="width: 12%">Gaji Pokok</th>
                                            <th class="text-end" style="width: 12%">Tunjangan Tetap</th>
                                            <th class="text-end" style="width: 12%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $nom_dept = 1; @endphp
                                        @foreach ($r['list_data'] as $r2)
                                        @php
                                        $gapok = $r2['gaji_pokok'] ?? 0;
                                        $tunj_tetap = $r2['tunj_tetap'] ?? 0;
                                        $total = $gapok + $tunj_tetap;
                                        $ket_status = '';
                                        foreach($list_status as $key => $value) {
                                            if($key==$r2['id_status_karyawan']) { $ket_status = $value; break; }
                                        }
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $nom_dept++ }}</td>
                                            <td>{{ $r2['nik'] }} - {{ $r2['nm_lengkap'] }}</td>
                                            <td class="fw-semibold">{{ $r2['jabatan'] }}</td>
                                            <td class="text-center"><span class="badge bg-light-info text-info">{{ $ket_status }}</span></td>
                                            <td class="text-end">{{ number_format($r2['gaji_pokok'], 0) }}</td>
                                            <td class="text-end">{{ number_format($tunj_tetap, 0) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($total, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="ti ti-hierarchy-2 me-2"></i>Hirarki Persetujuan
                    </h6>
                    <div class="table-responsive">
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
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card bg-light border-0 h-100">
                <div class="card-body">
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
