<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-target-arrow me-2"></i>Form Persetujuan KPI Departemen
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
<input type="hidden" name="status_approval" value="{{ $profil['kpiH']['status_pengajuan'] }}">
<input type="hidden" name="periode_bulan" id="periode_bulan" value="{{ $profil['kpiH']['bulan'] }}">
<input type="hidden" name="periode_tahun" id="periode_tahun" value="{{ $profil['kpiH']['tahun'] }}">
<input type="hidden" name="departemen" id="departemen" value="">
<div class="modal-body p-4">

    <div class="card bg-light border-0 mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <i class="ti ti-calendar text-success me-2"></i>
                <h6 class="fw-bold text-primary mb-0">Penilaian KPI Departemen - {{ $profil['periode_kpi'] }}</h6>
            </div>

            <ul class="nav nav-pills nav-fill mb-3" id="kpiTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="pill" href="#kpi-realisasi">
                        <i class="ti ti-chart-bar me-1"></i>KPI REALISASI
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="pill" href="#kpi-data-pendukung">
                        <i class="ti ti-files me-1"></i>DATA PENDUKUNG
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="kpi-realisasi" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th>KPI</th>
                                    <th style="width: 10%" class="text-center">Tipe</th>
                                    <th style="width: 10%" class="text-center">Satuan</th>
                                    <th style="width: 10%" class="text-center">Bobot (%)</th>
                                    <th style="width: 10%" class="text-center">Target (%)</th>
                                    <th style="width: 10%" class="text-center">Realisasi (%)</th>
                                    <th style="width: 10%" class="text-center">Skor Akhir (%)</th>
                                    <th style="width: 10%" class="text-center">Nilai KPI (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profil['detailKPI'] as $r1)
                                <tr>
                                    <td>{{ $r1->nama_kpi }}</td>
                                    <td class="text-center">{{ $r1->tipe }}</td>
                                    <td class="text-center">{{ $r1->satuan }}</td>
                                    <td class="text-center">{{ $r1->bobot }}</td>
                                    <td class="text-center">{{ $r1->target }}</td>
                                    <td class="text-center">{{ $r1->realisasi }}</td>
                                    <td class="text-center">{{ $r1->skor_akhir }}</td>
                                    <td class="text-center fw-semibold">{{ $r1->nilai_kpi }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-light">
                                    <td colspan="7" class="text-end fw-bold">TOTAL NILAI KPI (%)</td>
                                    <td class="text-center fw-bold text-primary">{{ $profil['kpiH']['total_kpi'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="kpi-data-pendukung" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-white">
                                <tr>
                                    <th>KPI</th>
                                    <th style="width: 40%">Data Pendukung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profil['LampiranKPI'] as $r3)
                                <tr>
                                    <td class="fw-semibold">{{ $r3['get_k_p_i_master']['nama_kpi'] }}</td>
                                    <td>{{ $r3['get_k_p_i_master']['data_pendukung'] }}</td>
                                </tr>
                                @if(count($r3['lampiran']) > 0)
                                <tr>
                                    <td colspan="2" class="p-2">
                                        <div class="bg-light rounded p-2">
                                            <div class="fw-semibold small text-muted mb-2">Lampiran data pendukung:</div>
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @foreach ($r3['lampiran'] as $r4)
                                                    <tr>
                                                        <td class="text-center" style="width: 5%">{{ $loop->iteration }}</td>
                                                        <td>{{ $r4->keterangan }}</td>
                                                        <td class="text-center" style="width: 25%">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="goDownloadLampiran(this)" id="{{ $r4->id }}">
                                                                <i class="ti ti-download me-1"></i>Download
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="2" class="text-center text-muted small">Tidak ada lampiran</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
    var goDownloadLampiran = function(el)
    {
        window.open("{{ url('hrd/kpi/downloadLampiranKPI') }}/"+el.id);
    }
</script>



