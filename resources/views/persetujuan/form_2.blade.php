<script src="{{ asset('assets/js/custom.js') }}"></script>
<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold" id="exampleModalCenteredScrollableTitle">
        <i class="ti ti-user-search me-2"></i>Form Persetujuan Pengajuan Aplikasi Pelamar
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
                        <i class="ti ti-user-search me-2"></i>Aplikasi Pelamar
                    </h6>
                    <div class="accordion" id="pelamarAccordion">

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingDiri">
                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiri" aria-expanded="true" aria-controls="collapseDiri">
                                    <i class="ti ti-user me-2"></i>Data Diri
                                </button>
                            </h2>
                            <div id="collapseDiri" class="accordion-collapse collapse show" aria-labelledby="headingDiri" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <img class="rounded border" id="preview_upload" src="{{ route('media.proxy', ['path' => $profil->id, 'type' => 'pelamar_photo']) }}" alt="profile-pic" style="width:120px; height:120px; object-fit:cover;">
                                        <div class="flex-fill">
                                            <h5 class="fw-bold mb-2">{{ $profil->nama_lengkap }}</h5>
                                            <div class="text-muted small mb-1"><i class="ti ti-phone me-1"></i>{{ $profil->no_hp }}</div>
                                            <div class="text-muted small mb-1"><i class="ti ti-brand-whatsapp me-1"></i>{{ $profil->no_wa }}</div>
                                            <div class="text-muted small mb-1"><i class="ti ti-map-pin me-1"></i>{{ $profil->alamat }}</div>
                                            <div class="text-muted small"><i class="ti ti-mail me-1"></i>{{ $profil->email }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingTes">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTes" aria-expanded="false" aria-controls="collapseTes">
                                    <i class="ti ti-clipboard-check me-2"></i>Hasil Tes
                                </button>
                            </h2>
                            <div id="collapseTes" class="accordion-collapse collapse" aria-labelledby="headingTes" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td colspan="2" class="fw-bold text-primary">PSIKOTEST</td></tr>
                                        <tr>
                                            <td class="text-muted" style="width:30%">Nilai</td>
                                            <td class="fw-semibold">{{ $profil->psikotes_nilai }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Keterangan</td>
                                            <td class="fw-semibold">{{ $profil->psikotes_ket }}</td>
                                        </tr>
                                        <tr><td colspan="2" class="fw-bold text-primary pt-3">WAWANCARA</td></tr>
                                        <tr>
                                            <td class="text-muted">Nilai</td>
                                            <td class="fw-semibold">{{ $profil->wawancara_nilai }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Keterangan</td>
                                            <td class="fw-semibold">{{ $profil->wawancara_ket }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="fw-bold">TOTAL SKOR</td>
                                            <td class="fw-bold text-success">{{ $profil->total_skor }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingLBK">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLBK" aria-expanded="false" aria-controls="collapseLBK">
                                    <i class="ti ti-users me-2"></i>Data Keluarga (Latar Belakang)
                                </button>
                            </h2>
                            <div id="collapseLBK" class="accordion-collapse collapse" aria-labelledby="headingLBK" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="text-muted small mb-2">Latar Belakang Keluarga (Ayah, Ibu & Saudara)</div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 12px">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Hubungan</th>
                                                    <th>Nama Keluarga</th>
                                                    <th>Tempat/Tanggal Lahir</th>
                                                    <th>Jenkel</th>
                                                    <th>Pendidikan</th>
                                                    <th>Pekerjaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $nom=1; @endphp
                                                @foreach($lb_keluarga as $lbk)
                                                <tr>
                                                    <td>{{ $nom }}</td>
                                                    <td>{{ $lbk->get_hubungan_keluarga($lbk->id_hubungan) }}</td>
                                                    <td>{{ $lbk->nm_keluarga }}</td>
                                                    <td>{{ $lbk->tmp_lahir.", ".date_format(date_create($lbk->tgl_lahir), 'd-m-Y') }}</td>
                                                    <td>{{ ($lbk->jenkel==1)? "Laki-Laki" : "Perempuan" }}</td>
                                                    <td>{{ $lbk->get_pendidikan_akhir($lbk->id_jenjang) }}</td>
                                                    <td>{{ $lbk->pekerjaan }}</td>
                                                </tr>
                                                @php $nom++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($profil->status_nikah==2)
                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingKel">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKel" aria-expanded="false" aria-controls="collapseKel">
                                    <i class="ti ti-users me-2"></i>Data Keluarga (Suami/Istri & Anak)
                                </button>
                            </h2>
                            <div id="collapseKel" class="accordion-collapse collapse" aria-labelledby="headingKel" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 12px">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Hubungan</th>
                                                    <th>Nama Keluarga</th>
                                                    <th>Tempat/Tanggal Lahir</th>
                                                    <th>Jenkel</th>
                                                    <th>Pendidikan</th>
                                                    <th>Pekerjaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $nom=1; @endphp
                                                @foreach($keluarga as $kel)
                                                <tr>
                                                    <td>{{ $nom }}</td>
                                                    <td>{{ $kel->get_hubungan_keluarga($kel->id_hubungan) }}</td>
                                                    <td>{{ $kel->nm_keluarga }}</td>
                                                    <td>{{ $kel->tmp_lahir.", ".date_format(date_create($kel->tgl_lahir), 'd-m-Y') }}</td>
                                                    <td>{{ ($kel->jenkel==1)? "Laki-Laki" : "Perempuan" }}</td>
                                                    <td>{{ $kel->get_pendidikan_akhir($kel->id_jenjang) }}</td>
                                                    <td>{{ $kel->pekerjaan }}</td>
                                                </tr>
                                                @php $nom++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingPend">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePend" aria-expanded="false" aria-controls="collapsePend">
                                    <i class="ti ti-school me-2"></i>Riwayat Pendidikan
                                </button>
                            </h2>
                            <div id="collapsePend" class="accordion-collapse collapse" aria-labelledby="headingPend" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 12px">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Jenjang</th>
                                                    <th>Nama Sekolah/PT</th>
                                                    <th>Alamat</th>
                                                    <th>Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $nom=1; @endphp
                                                @foreach($pendidikan as $jenj)
                                                <tr>
                                                    <td>{{ $nom }}</td>
                                                    <td>{{ $jenj->get_jenjang_pendidikan($jenj->id_jenjang) }}</td>
                                                    <td>{{ $jenj->nm_sekolah_pt }}</td>
                                                    <td>{{ $jenj->alamat }}</td>
                                                    <td>{{ $jenj->mulai_tahun." s/d ".$jenj->sampai_tahun }}</td>
                                                </tr>
                                                @php $nom++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingOrg">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrg" aria-expanded="false" aria-controls="collapseOrg">
                                    <i class="ti ti-id me-2"></i>Pengalaman Organisasi
                                </button>
                            </h2>
                            <div id="collapseOrg" class="accordion-collapse collapse" aria-labelledby="headingOrg" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 12px">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nama Organisasi</th>
                                                    <th>Posisi/Jabatan</th>
                                                    <th>Periode</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $nom=1; @endphp
                                                @foreach($organisasi as $org)
                                                <tr>
                                                    <td>{{ $nom }}</td>
                                                    <td>{{ $org->nama_organisasi }}</td>
                                                    <td>{{ $org->posisi }}</td>
                                                    <td>{{ $org->mulai_tahun." s/d ".$org->sampai_tahun }}</td>
                                                </tr>
                                                @php $nom++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border mb-2">
                            <h2 class="accordion-header" id="headingKerja">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKerja" aria-expanded="false" aria-controls="collapseKerja">
                                    <i class="ti ti-briefcase me-2"></i>Pengalaman Kerja
                                </button>
                            </h2>
                            <div id="collapseKerja" class="accordion-collapse collapse" aria-labelledby="headingKerja" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" style="font-size: 12px">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nama Perusahaan</th>
                                                    <th>Posisi/Jabatan</th>
                                                    <th>Alamat</th>
                                                    <th>Periode</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $nom=1; @endphp
                                                @foreach($pekerjaan as $krj)
                                                <tr>
                                                    <td>{{ $nom }}</td>
                                                    <td>{{ $krj->nm_perusahaan }}</td>
                                                    <td>{{ $krj->posisi }}</td>
                                                    <td>{{ $krj->alamat }}</td>
                                                    <td>{{ $krj->mulai_tahun." s/d ".$krj->sampai_tahun }}</td>
                                                </tr>
                                                @php $nom++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border">
                            <h2 class="accordion-header" id="headingDok">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDok" aria-expanded="false" aria-controls="collapseDok">
                                    <i class="ti ti-paperclip me-2"></i>Dokumen
                                </button>
                            </h2>
                            <div id="collapseDok" class="accordion-collapse collapse" aria-labelledby="headingDok" data-bs-parent="#pelamarAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach($jenis_dokumen as $dok)
                                        <div class="text-center" style="width:120px;">
                                            @foreach($list_dokumen as $dtdok => $valdok)
                                                @if($valdok->id_dokumen==$dok->id)
                                                    <a href="{{ route('media.proxy', ['path' => $valdok->id, 'type' => 'pelamar_dokumen']) }}" data-fancybox data-caption='{{ $dok->id.". ".$dok->nm_dokumen}}'>
                                                        <img src="{{ route('media.proxy', ['path' => $valdok->id, 'type' => 'pelamar_dokumen']) }}" alt="{{ $dok->nm_dokumen }}" class="rounded border" style="width:100px; height:100px; object-fit:cover;">
                                                    </a>
                                                    @php break; @endphp
                                                @endif
                                            @endforeach
                                            <div class="small fw-semibold mt-1">{{ $dok->nm_dokumen }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
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
                        <table class="table table-sm table-hover mb-0" style="font-size: 11px">
                            <thead class="bg-white">
                                <tr>
                                    <th rowspan="2" class="text-center" style="width: 5%">Lvl</th>
                                    <th rowspan="2">Pejabat</th>
                                    <th colspan="3" class="text-center">Persetujuan</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Tgl</th>
                                    <th class="text-center">Ket</th>
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
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $list->get_profil_employee->get_jabatan->nm_jabatan }}</div>
                                    </td>
                                    <td class="text-center">{{ (empty($list->approval_date)) ? "-" : date('d-m-Y', strtotime($list->approval_date))  }}</td>
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
                                <option value="3">Pending</option>
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



