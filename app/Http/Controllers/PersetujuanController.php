<?php

namespace App\Http\Controllers;

use App\Helpers\HrdFunction;
use App\Models\Approval;
use App\Models\CutiPerubahan;
use App\Models\Departemen;
use App\Models\ExitInterviews;
use App\Models\JenisDokumenKaryawan;
use App\Models\Karyawan;
use App\Models\KPIPeriodik;
use App\Models\KPIPeriodikDetail;
use App\Models\KPIPeriodikLampiran;
use App\Models\Leave;
use App\Models\Mutasi;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\PayrollHeader;
use App\Models\Pelamar;
use App\Models\PelamarDokumen;
use App\Models\PelamarKeluarga;
use App\Models\PelamarLBKeluarga;
use App\Models\PelamarOrganisasi;
use App\Models\PelamarPengalamanKerja;
use App\Models\PelamarRiwayatPendidikan;
use App\Models\PengajuanPelatihanHeader;
use App\Models\Perdis;
use App\Models\Permission;
use App\Models\PerubahanStatus;
use App\Models\PinjamanKaryawan;
use App\Models\RecruitmentPengajuanTK;
use App\Models\ReprimandLetter;
use App\Models\Resign;
use App\Models\SPNonAktif;
use App\Models\Thr;
use App\Models\ThrDetail;
use App\Models\TrainingHead;
use App\Models\WarningLetter;
use Illuminate\Http\Request;
use App\Traits\GenerateNumber;
use Illuminate\Support\Facades\Config;

class PersetujuanController extends Controller
{
    use GenerateNumber;
    public function index()
    {
        $data['list_persetujuan'] = Approval::with(['get_ref_approval', 'get_profil_employee'])->where('approval_active', 1)->where('approval_by_employee', auth()->user()->karyawan->id)->get()->map(function ($row) {
            $arr = $row->toArray();
            $arr['detail'] = [
                'tgl_pengajuan' => null,
                'departemen' => '-',
                'catatan_pengajuan' => '-',
                'diajukan_oleh' => '-',
            ];
            if($arr['approval_group']==1) //pengajuan permintaan karyawan
            {
                $result = RecruitmentPengajuanTK::where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tanggal_pengajuan,
                    "departemen" => $result?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->catatan ?? '-',
                    'diajukan_oleh' => $result?->user_create?->karyawan?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==2) //pengajuan aplikasi pelamar
            {
                $result = Pelamar::where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->get_departmen?->nm_dept ?? '-',
                    'catatan_pengajuan' => "Jabatan yang dilamar : ".($result?->get_jabatan?->nm_jabatan ?? '-'),
                    'diajukan_oleh' => $result?->nama_lengkap ?? '-',
                    'status_app' => $result?->status_app
                ];
            }
            if($arr['approval_group']==3) //pengajuan Cuti
            {
                $result = Leave::where('sts_pengajuan', 1)->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->ket_cuti ?? '-',
                    'diajukan_oleh' => $result?->profil_karyawan?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==4) //pengajuan izin
            {
                $result = Permission::where('sts_pengajuan', 1)->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->ket_izin ?? '-',
                    'diajukan_oleh' => $result?->profil_karyawan?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==5) //pengajuan perubahan Status
            {
                $result = PerubahanStatus::with(['get_departemen'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_pengajuan ?? '-',
                    'diajukan_oleh' => $result?->get_create_by($result?->id_user)?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==6) //pengajuan mutasi
            {
                $result = Mutasi::where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_pengajuan ?? '-',
                    'diajukan_oleh' => $result?->get_submit_by?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==7) //pengajuan Lembur
            {
                $result = Overtime::with(['get_profil_karyawan'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->get_profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->deskripsi_pekerjaan ?? '-',
                    'diajukan_oleh' => $result?->get_profil_karyawan?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==8) //pengajuan Perjalanan Dinas
            {
                $result = Perdis::with(['get_profil'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->get_profil?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->maksud_tujuan ?? '-',
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==9) //pengajuan Pelatihan
            {
                $result = PengajuanPelatihanHeader::with(['get_diajukan_oleh'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->get_diajukan_oleh?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->deskripsi ?? '-',
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==10) //pengajuan Surat Teguran
            {
                $result = ReprimandLetter::with(['get_karyawan', 'get_jenis_pelanggaran', 'get_diajukan_oleh'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tanggal_pengajuan,
                    "departemen" => $result?->get_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->get_jenis_pelanggaran?->jenis_pelanggaran ?? '-',
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==11) //pengajuan Surat Peringatan
            {
                $result = WarningLetter::with(['profil_karyawan', 'get_master_jenis_sp_diajukan', 'get_diajukan_oleh'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->uraian_pelanggaran ?? '-',
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==12) //pengajuan Penggajian/Payroll
            {
                $result = PayrollHeader::with(['get_diajukan_oleh'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->get_diajukan_oleh?->karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => "Data Gaji Karyawan Periode ".HrdFunction::get_nama_bulan($result?->bulan)." Tahun ".($result?->tahun ?? '-'),
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->karyawan?->nm_lengkap ?? '-',
                ];
            }
            if($arr['approval_group']==13) //pengajuan Pinjaman Karyawan
            {
                $result = PinjamanKaryawan::with(['getKaryawan'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->tgl_pengajuan,
                    "departemen" => $result?->getKaryawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_pengajuan ?? '-',
                    'diajukan_oleh' => $result?->getKaryawan?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==14) //pengajuan pengajuan perubahan masa cuti
            {
                $result = CutiPerubahan::with([
                    'get_cuti_origin',
                    'get_cuti_origin.profil_karyawan',
                    'get_create_by'
                ])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->get_cuti_origin?->profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_perubahan ?? '-',
                    'diajukan_oleh' => $result?->get_create_by?->karyawan?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==15) //pengajuan Resign
            {
                $result = Resign::with(['getKaryawan'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->getKaryawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_resign ?? '-',
                    'diajukan_oleh' => $result?->getKaryawan?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==16) //exit interview form
            {
                $result = ExitInterviews::with([
                    'getPengajuan.getKaryawan'
                    ])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->getPengajuan?->getKaryawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => "Form Exit Interviews",
                    'diajukan_oleh' => $result?->getPengajuan?->getKaryawan?->nm_lengkap ?? '-'
                ];
            }
            if($arr['approval_group']==17) //penonaktifan SP
            {
                $result = SPNonAktif::with([
                    'getSP',
                    'getSP.profil_karyawan'
                    ])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->getSP?->profil_karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => $result?->alasan_non_aktif ?? '-',
                    'diajukan_oleh' => $result?->getCreateBy?->karyawan?->nm_lengkap ?? '-'
                ];
            }

            if($arr['approval_group']==18) //pengajuan THR
            {
                $result = Thr::with(['get_diajukan_oleh'])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->created_at,
                    "departemen" => $result?->get_diajukan_oleh?->karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => "Data Tunjangan Hari Raya Karyawan Periode ".hrdfunction::get_nama_bulan($result?->bulan)." Tahun ".($result?->tahun ?? '-'),
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->karyawan?->nm_lengkap ?? '-',
                ];
            }

            if($arr['approval_group']==19) //pengajuan KPI Periodik
            {
                $result = KPIPeriodik::with([
                    'get_diajukan_oleh',
                    'getDepartemen'
                ])->where('approval_key', $arr['approval_key'])->first();
                $arr['detail'] = [
                    "tgl_pengajuan" => $result?->submit_at,
                    "departemen" => $result?->get_diajukan_oleh?->karyawan?->get_departemen?->nm_dept ?? '-',
                    'catatan_pengajuan' => "Penialain KPI Departemen ".($result?->getDepartemen?->nm_dept ?? '-')." Periode ".hrdfunction::get_nama_bulan($result?->bulan)." Tahun ".($result?->tahun ?? '-'),
                    'diajukan_oleh' => $result?->get_diajukan_oleh?->karyawan?->nm_lengkap ?? '-',
                ];
            }

            return $arr;

        });
        // dd($data);

        return view("persetujuan.index", $data);
    }

    public function form_approval($id)
    {
        $query = Approval::find($id);
        $approval_key = $query->approval_key;
        $data['data_approval'] = $query;
        $data['hirarki_persetujuan'] = Approval::where('approval_key', $approval_key)->orderBy('approval_level')->get();
        if($query->approval_group==1) { //Pengajuan Permintaan Karyawan
            $data['profil'] = RecruitmentPengajuanTK::with([
                'get_departemen',
                'get_jabatan',
            ])
            ->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_1", $data);
        }
        if($query->approval_group==2) { //Pengajuan Aplikasi Pelamar
            $data['profil'] = Pelamar::where('approval_key', $approval_key)->first();
            $data['lb_keluarga'] = PelamarLBKeluarga::where('id_pelamar', $data['profil']->id)->get();
            $data['keluarga'] = PelamarKeluarga::where('id_pelamar', $data['profil']->id)->get();
            $data['pendidikan'] = PelamarRiwayatPendidikan::where('id_pelamar', $data['profil']->id)->get();
            $data['organisasi'] = PelamarOrganisasi::where("id_pelamar", $data['profil']->id)->get();
            $data['pekerjaan'] = PelamarPengalamanKerja::where("id_pelamar", $data['profil']->id)->get();
            $data['list_dokumen'] = PelamarDokumen::where('id_pelamar', $data['profil']->id)->get();
            $data['jenis_dokumen'] = JenisDokumenKaryawan::where('pelamar', 1)->get();
            return view("persetujuan.form_2", $data);
        }
        if($query->approval_group==3) { //Pengajuan Cuti
            $data['profil'] = Leave::where('approval_key', $approval_key)->first();
            $data['listKaryawan'] = Karyawan::where('id_departemen', auth()->user()->karyawan->id_departemen)
                        ->whereNotIn('id', [$data['profil']->id_karyawan])
                        ->get();
            return view("persetujuan.form_3", $data);
        }
        if($query->approval_group==4) { //Pengajuan Izin
            $data['profil'] = Permission::where('approval_key', $approval_key)->first();
            return view("persetujuan.form_4", $data);
        }
        if($query->approval_group==5) { //Pengajuan perubahan status
            $data['profil'] = PerubahanStatus::where('approval_key', $approval_key)->first();
            return view("persetujuan.form_5", $data);
        }
        if($query->approval_group==6) { //Pengajuan mutasi
            $data['profil'] = Mutasi::where('approval_key', $approval_key)->first();
            $res_kategori = Config::get('constants.kategori_mutasi');
            if(empty($data['profil']->get_profil->tgl_masuk)) {
                $ket_lama_kerja = "";
                $tgl_masuk = "Tanpa Keterangan";
            } else {
                $ket_lama_kerja = HrdFunction::get_lama_kerja_karyawan($data['profil']->get_profil->tgl_masuk);
                $tgl_masuk = date_format(date_create($data['profil']->get_profil->tgl_masuk), "d-m-Y");
            }
            if(empty($data['profil']->get_profil->tmt_jabatan))
            {
                $tgl_eff_jabatan = date("Y-m-d");
            } else {
                $tgl_eff_jabatan = date("d-m-Y", strtotime($data['profil']->get_profil->tmt_jabatan));
            }
            $data['list_kategori'] = $res_kategori;
            $data['ket_lama_kerja'] = $ket_lama_kerja;
            $data['ket_tgl_masuk'] = $tgl_masuk;
            $data['ket_efektif_jabatan'] = $tgl_eff_jabatan;

            return view("persetujuan.form_6", $data);
        }
        if($query->approval_group==7) { //Pengajuan Lembur
            $data['profil'] = Overtime::where('approval_key', $approval_key)->first();
            return view("persetujuan.form_7", $data);
        }
        if($query->approval_group==8) { //Pengajuan Perjalanan dinas
            $data['profil'] = Perdis::where('approval_key', $approval_key)->first();
            return view("persetujuan.form_8", $data);
        }
        if($query->approval_group==9) { //Pengajuan pelatihan
            $data['profil'] = PengajuanPelatihanHeader::with([
                'get_detail',
                'get_detail.getPelatihan',
                'get_detail.getPelatihan.get_departemen'
            ])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_9", $data);
        }
        if($query->approval_group==10) { //Pengajuan Surat Teguran
            $data['profil'] = ReprimandLetter::where('approval_key', $approval_key)->first();
            return view("persetujuan.form_10", $data);
        }
        if($query->approval_group==11) { //Pengajuan Surat Peringatan
            $data['profil'] = WarningLetter::with(['profil_karyawan', 'get_master_jenis_sp_diajukan', 'get_diajukan_oleh'])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_11", $data);
        }
        if($query->approval_group==12) { //Pengajuan penggajian/payroll
            $payroll = PayrollHeader::with(['get_diajukan_oleh'])->where('approval_key', $approval_key)->first();
            $bulan = $payroll->bulan;
            $tahun = $payroll->tahun;
            $allDepartemen = Departemen::where('status', 1)
                ->get()->map( function ($row) use ($bulan, $tahun)
                {
                    $arr = $row->toArray();
                    $arr['total'] = Payroll::where('id_departemen', $arr['id'])
                                    ->where('bulan', $bulan)
                                    ->where('tahun', $tahun)
                                    ->sum('thp');
                    return $arr;
                });
            $nonDepartemen = Karyawan::leftJoin('hrd_payroll', 'hrd_payroll.id_karyawan', '=', 'hrd_karyawan.id')
                        ->where(function ($q) {
                        $q->whereNull('hrd_karyawan.id_departemen')
                            ->orWhere('hrd_karyawan.id_departemen', 0);
                        })
                        ->where('hrd_payroll.bulan', $bulan)
                        ->where('hrd_payroll.tahun', $tahun)
                        ->whereIn('id_status_karyawan', [1, 2, 3])
                        ->get()->sum('hrd_payroll.thp');

            $resNonDepartemen = Karyawan::with(['get_jabatan'])->whereNull('id_departemen')->where('nik', '<>', '999999999')->get()
            ->map(function ($row) use ($bulan, $tahun) {
                $arr = $row->toArray();
                $id_karyawan = $arr['id'];
                $payrollData = Payroll::where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('id_karyawan', $id_karyawan)
                    ->first();
                $list_data = $payrollData;

                $arr['list_data'] = [
                    'id_karyawan' => $id_karyawan,
                    'gaji_pokok' => $payrollData->gaji_pokok ?? 0,
                    'tunj_perusahaan' => $payrollData->tunj_perusahaan ?? 0,
                    'tunj_tetap' => $payrollData->tunj_tetap ?? 0,
                    'hours_meter' => $payrollData->hours_meter ?? 0,
                    'pot_tunj_perusahaan' => $payrollData->pot_tunj_perusahaan ?? 0,
                    'bpjsks_karyawan' => $payrollData->bpjsks_karyawan ?? 0,
                    'bpjstk_jht_karyawan' => $payrollData->bpjstk_jht_karyawan ?? 0,
                    'bpjstk_jp_karyawan' => $payrollData->bpjstk_jp_karyawan ?? 0,
                    'bpjstk_jkm_karyawan' => $payrollData->bpjstk_jkm_karyawan ?? 0,
                    'bpjstk_jkk_karyawan' => $payrollData->bpjstk_jkk_karyawan ?? 0,
                    'bpjsks_perusahaan' => $payrollData->bpjsks_perusahaan ?? 0,
                    'bpjstk_jht_perusahaan' => $payrollData->bpjstk_jht_perusahaan ?? 0,
                    'bpjstk_jp_perusahaan' => $payrollData->bpjstk_jp_perusahaan ?? 0,
                    'bpjstk_jkm_perusahaan' => $payrollData->bpjstk_jkm_perusahaan ?? 0,
                    'bpjstk_jkk_perusahaan' => $payrollData->bpjstk_jkk_perusahaan ?? 0,
                    'lembur' => $payrollData->lembur ?? 0,
                    'pot_sedekah' => $payrollData->pot_sedekah ?? 0,
                    'pot_pkk' => $payrollData->pot_pkk ?? 0,
                    'pot_air' => $payrollData->pot_air ?? 0,
                    'pot_rumah' => $payrollData->pot_rumah ?? 0,
                    'pot_toko_alif' => $payrollData->pot_toko_alif ?? 0,
                    'status_payroll' => $payrollData ? 'Terdaftar' : 'Belum Terdaftar',
                    // Tambahkan data lain sesuai kebutuhan
                ];
                return $arr;
            });
            $resPenggajian = Departemen::where('status', 1)->get()->map( function ($row) use ($bulan, $tahun) {
                $arr = $row->toArray();
                // Ambil semua karyawan di departemen ini
                $karyawan = Karyawan::with(['get_jabatan'])->whereIn('id_status_karyawan', [1, 2, 3])->where('id_departemen', $arr['id'])->get();
                $payrollData = Payroll::where('id_departemen', $arr['id'])
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->get()
                    ->keyBy('id_karyawan'); // agar bisa dicari cepat
                // Gabungkan data karyawan + payroll (jika ada)
                $list_data = $karyawan->map(function ($karyawan) use ($payrollData) {
                    $payroll = $payrollData->get($karyawan->id);
                    return [
                        'id_karyawan' => $karyawan->id,
                        'nm_lengkap' => $karyawan->nm_lengkap,
                        'nik' => $karyawan->nik,
                        'jabatan' => $karyawan->get_jabatan->nm_jabatan,
                        'gaji_pokok' => $payroll->gaji_pokok ?? 0,
                        'tunj_perusahaan' => $payroll->tunj_perusahaan ?? 0,
                        'tunj_tetap' => $payroll->tunj_tetap ?? 0,
                        'hours_meter' => $payroll->hours_meter ?? 0,
                        'pot_tunj_perusahaan' => $payroll->pot_tunj_perusahaan ?? 0,
                        'bpjsks_karyawan' => $payroll->bpjsks_karyawan ?? 0,
                        'bpjstk_jht_karyawan' => $payroll->bpjstk_jht_karyawan ?? 0,
                        'bpjstk_jp_karyawan' => $payroll->bpjstk_jp_karyawan ?? 0,
                        'bpjstk_jkm_karyawan' => $payroll->bpjstk_jkm_karyawan ?? 0,
                        'bpjstk_jkk_karyawan' => $payroll->bpjstk_jkk_karyawan ?? 0,
                        'bpjsks_perusahaan' => $payroll->bpjsks_perusahaan ?? 0,
                        'bpjstk_jht_perusahaan' => $payroll->bpjstk_jht_perusahaan ?? 0,
                        'bpjstk_jp_perusahaan' => $payroll->bpjstk_jp_perusahaan ?? 0,
                        'bpjstk_jkm_perusahaan' => $payroll->bpjstk_jkm_perusahaan ?? 0,
                        'bpjstk_jkk_perusahaan' => $payroll->bpjstk_jkk_perusahaan ?? 0,
                        'lembur' => $payroll->lembur ?? 0,
                        'pot_sedekah' => $payroll->pot_sedekah ?? 0,
                        'pot_pkk' => $payroll->pot_pkk ?? 0,
                        'pot_air' => $payroll->pot_air ?? 0,
                        'pot_rumah' => $payroll->pot_rumah ?? 0,
                        'pot_toko_alif' => $payroll->pot_toko_alif ?? 0,
                        'status_payroll' => $payroll ? 'Terdaftar' : 'Belum Terdaftar',
                        // Tambahkan data lain sesuai kebutuhan
                    ];
                });
                $arr['list_data'] = $list_data;
                return $arr;
            });

            $data['profil'] = $payroll;
            $data['resumeDepartemen'] = $allDepartemen;
            $data['resumeNonDepartemen'] = $nonDepartemen;
            $data['data_non_dept'] = $resNonDepartemen;
            $data['data_dept'] = $resPenggajian;

            // dd($data);
            return view("persetujuan.form_12", $data);
        }
        if($query->approval_group==13) { //Pengajuan Pinjaman Karyawan
            $data['profil'] = PinjamanKaryawan::with(['getKaryawan', 'getDokumen'])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_13", $data);
        }
        if($query->approval_group==14) { //Pengajuan Perubahan Masa Cuti
            $data['profil'] = CutiPerubahan::with([
                'get_cuti_origin',
                'get_cuti_origin.profil_karyawan'
            ])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_14", $data);
        }
        //pengajuan resign 15
        if($query->approval_group==15) { //Pengajuan Resign
            $data['profil'] = Resign::with([
                'getKaryawan',
            ])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_15", $data);
        }
        //pengajuan resign - exit form 16
        if($query->approval_group==16) { //Pengajuan Resign
            $data['profil'] = ExitInterviews::with([
                'getPengajuan',
                'getPengajuan.getKaryawan',
            ])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_16", $data);
        }
        //pengajuan penonaktifan SP - 17
        if($query->approval_group==17) { //Penonaktifan SP
            $data['profil'] = SPNonAktif::with([
                'getSP',
                'getSP.profil_karyawan',
            ])->where('approval_key', $approval_key)->first();
            return view("persetujuan.form_17", $data);
        }
        //Pengajuan THR
        if($query->approval_group==18) {
            $payroll = Thr::with(['get_diajukan_oleh'])->where('approval_key', $approval_key)->first();
            $bulan = $payroll->bulan;
            $tahun = $payroll->tahun;
            $resNonDepartemen = Karyawan::with(['get_jabatan'])->whereNull('id_departemen')->where('nik', '<>', '999999999')->get()
            ->map(function ($row) use ($bulan, $tahun) {
                $arr = $row->toArray();
                $id_karyawan = $arr['id'];
                $payrollData = ThrDetail::where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('id_karyawan', $id_karyawan)
                    ->first();
                $list_data = $payrollData;

                $arr['list_data'] = [
                    'id_karyawan' => $id_karyawan,
                    'gaji_pokok' => $payrollData->gaji_pokok ?? 0,
                    'tunj_tetap' => $payrollData->tunj_tetap ?? 0,
                    // Tambahkan data lain sesuai kebutuhan
                ];
                return $arr;
            });
            $resPenggajian = Departemen::where('status', 1)->get()->map( function ($row) use ($bulan, $tahun) {
                $arr = $row->toArray();
                // Ambil semua karyawan di departemen ini
                $karyawan = Karyawan::with(['get_jabatan'])->whereIn('id_status_karyawan', [1, 2, 3, 7])->where('id_departemen', $arr['id'])->get();
                $payrollData = ThrDetail::where('id_departemen', $arr['id'])
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->get()
                    ->keyBy('id_karyawan'); // agar bisa dicari cepat
                // Gabungkan data karyawan + payroll (jika ada)
                $list_data = $karyawan->map(function ($karyawan) use ($payrollData) {
                    $payroll = $payrollData->get($karyawan->id);
                    return [
                        'id_karyawan' => $karyawan->id,
                        'nm_lengkap' => $karyawan->nm_lengkap,
                        'nik' => $karyawan->nik,
                        'jabatan' => $karyawan->get_jabatan->nm_jabatan,
                        'id_status_karyawan' => $karyawan->id_status_karyawan,
                        'gaji_pokok' => $payroll->gaji_pokok ?? 0,
                        'tunj_tetap' => $payroll->tunj_tetap ?? 0,
                        // Tambahkan data lain sesuai kebutuhan
                    ];
                });
                $arr['list_data'] = $list_data;
                return $arr;
            });

            $data['profil'] = $payroll;
            $data['data_non_dept'] = $resNonDepartemen;
            $data['data_dept'] = $resPenggajian;
            $data['list_status'] = Config::get("constants.status_karyawan");
            // dd($data);
            return view("persetujuan.form_18", $data);
        }
        if($query->approval_group==19) { //KPI Periodik
            $dataH = KPIPeriodik::with(['getDepartemen'])->where('approval_key', $approval_key)->first();
            $id_head = $dataH->id;
            $data['profil'] = [
                "kpiH" => $dataH,
                "detailKPI" => KPIPeriodikDetail::where('id_head', $dataH->id)->get(),
                'LampiranKPI' => KPIPeriodikDetail::with([
                        'getKPIMaster',
                        'getKPIMaster.tipeKPI',
                        'getKPIMaster.satuanKPI'
                    ])->where('id_head', $dataH->id)->get()->map( function($newRow) use ($id_head){
                        $arr = $newRow->toArray();
                        $arr['lampiran'] = KPIPeriodikLampiran::where('id_head', $id_head)->where('id_detail_kpi', $arr['id'])->get();
                        return $arr;
                    }),
                'periode_kpi' => $dataH->getDepartemen->nm_dept." Periode ". HrdFunction::get_nama_bulan($dataH->bulan). " ".$dataH->tahun,
            ];
            return view("persetujuan.form_19", $data);
        }
    }

    public function store_approval(Request $request)
    {
        $id = $request->id_pengajuan;
        $data_approval = Approval::find($id);
        if($request->pil_persetujuan < 3)
        {
            $data_approval->approval_active = 0; //Approved
        }
        $data_approval->approval_date = date('Y-m-d');
        $data_approval->approval_remark = $request->inp_keterangan;
        $data_approval->approval_status = $request->pil_persetujuan;
        $data_approval->save();
        //next level
        $next_id = NULL;
        if($request->pil_persetujuan==1)
        {
            $next_level = (int)$request->level_approval + 1;
            $rowNext = Approval::where('approval_key', $request->key_approval)
                ->where('approval_level', $next_level)
                ->where('approval_group', $request->group_approval)
                ->orderBy('created_at', 'desc')->first();
            if(!empty(($rowNext->id)))
            {
                $update_next_row = Approval::find($rowNext->id);
                $update_next_row->approval_active = 1;
                $update_next_row->save();
                $next_id = $rowNext->approval_by_employee;

                //update current Approval
                if($data_approval->approval_group==1) //permintaan tenaga kerja
                {
                    $main_data = RecruitmentPengajuanTK::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==2) //Aplikasi Pelamar
                {
                    $main_data = Pelamar::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==3) //Cuti
                {
                    $main_data = Leave::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    if($request->level_approval==1) {
                        $main_data->id_pengganti = $request->pil_pengganti;
                    }
                    $main_data->save();
                }
                if($data_approval->approval_group==4) //Izin
                {
                    $main_data = Permission::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==5) //Aplikasi Perubahan Status
                {
                    $main_data = PerubahanStatus::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==6) //Aplikasi Mutasi
                {
                    $main_data = Mutasi::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==7) //Lembur
                {
                    $main_data = Overtime::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->save();
                }
                if($data_approval->approval_group==8) //Perjalanan Dinas
                {
                    $main_data = Perdis::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->save();
                }
                if($data_approval->approval_group==9) //Pengajuan Pelatihan
                {
                    $main_data = PengajuanPelatihanHeader::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==10) //Pengajuan Surat teguran
                {
                    $main_data = ReprimandLetter::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==11) //Pengajuan Surat Peringatan
                {
                    $main_data = WarningLetter::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==12) //Pengajuan Penggajian
                {
                    $main_data = PayrollHeader::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==13) //Pengajuan Pinjaman Karyawan
                {
                    $main_data = PinjamanKaryawan::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==14) //Perubahan Masa Cuti
                {
                    $main_data = CutiPerubahan::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==15) //Pengajuan Resign
                {
                    $main_data = Resign::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==16) //Pengajuan Resign
                {
                    $main_data = ExitInterviews::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==17) //Pengajuan penonaktifan sp
                {
                    $main_data = SPNonAktif::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==18) //Pengajuan THR
                {
                    $main_data = Thr::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->is_draft = 2;
                    $main_data->save();
                }
                if($data_approval->approval_group==19) //Pengajuan KPI
                {
                    $main_data = KPIPeriodik::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->current_approval_id = $next_id;
                    $main_data->save();
                }

            } else {
                //update if Approval
                if($data_approval->approval_group==1) //permintaan tenaga kerja
                {
                    $main_data = RecruitmentPengajuanTK::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==2) //Aplikasi Pelamar
                {
                    $main_data = Pelamar::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==3) //Cuti
                {

                    $main_data = Leave::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->nomor_surat = GenerateNumber::generate_no_surat();
                    $main_data->tanggal_surat = date("Y-m-d");
                    $main_data->save();
                }
                if($data_approval->approval_group==4) //Izin
                {
                    $main_data = Permission::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==5) //Aplikasi Perubahan Status
                {
                    $main_data = PerubahanStatus::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==6) //Aplikasi Mutasi
                {
                    $main_data = Mutasi::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==7) //lembur
                {
                    $main_data = Overtime::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==8) //Perjalanan Dinas
                {
                    $main_data = Perdis::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==9) //Pengajuan Pelatihan
                {
                    $main_data = PengajuanPelatihanHeader::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->is_draft = 2;
                    $main_data->save();
                    //update pelatihan header
                    $all_pilihan_pelatihan = PengajuanPelatihanHeader::select([
                        'hrd_pengajuan_pelatihan_d.id_pelatihan'
                            ])
                            ->leftJoin('hrd_pengajuan_pelatihan_d', 'hrd_pengajuan_pelatihan_d.id_head', '=', 'hrd_pengajuan_pelatihan_h.id')
                            ->where('hrd_pengajuan_pelatihan_h.approval_key', $data_approval->approval_key)->get();
                    foreach($all_pilihan_pelatihan as $item)
                    {
                        $update_head = TrainingHead::find($item->id_pelatihan);
                        $update_head->status_pelatihan = 2; //approve
                        $update_head->update();
                    }
                }
                if($data_approval->approval_group==10) //Pengajuan Surat Teguran
                {
                    $main_data = ReprimandLetter::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==11) //Pengajuan Surat Peringatan
                {
                    $main_data = WarningLetter::where('approval_key', $data_approval->approval_key)->first();
                    $masa_berlaku_sp = HrdFunction::get_masa_berlaku_sp($main_data->id_jenis_sp_disetujui);
                    $start_sp = date('Y-m-d');
                    $end_sp = date('Y-m-d', strtotime($start_sp . ' +'.$masa_berlaku_sp.' month'));

                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->tgl_sp = date('Y-m-d');
                    $main_data->no_sp = $this->get_nomor_surat_sp(); // `SuratPeringatanController::buat_nomorsurat_baru();
                    $main_data->sp_lama_active = $masa_berlaku_sp;
                    $main_data->sp_mulai_active = $start_sp;
                    $main_data->sp_akhir_active = $end_sp;
                    $main_data->save();
                    //update sp aktive karyawan
                    $id_karyawan = $main_data->id_karyawan;
                    $current_active = Karyawan::find($id_karyawan);
                    $current_active->sp_active = "active";
                    $current_active->sp_level_active = $main_data->id_jenis_sp_disetujui;
                    $current_active->sp_lama_active = $masa_berlaku_sp;
                    $current_active->sp_mulai_active = $start_sp;
                    $current_active->sp_akhir_active = $end_sp;
                    $current_active->sp_reff = $main_data->id;
                    $current_active->save();
                }
                if($data_approval->approval_group==12) //Pengajuan Penggajian
                {
                    $main_data = PayrollHeader::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                    Payroll::where('bulan', $main_data->bulan)->where('tahun', $main_data->tahun)->update([
                        'flag' => 1
                    ]);
                    // $update_payroll->flag = 1;
                    // $update_payroll->update();
                }
                if($data_approval->approval_group==13) //Pengajuan Pinjaman Karyawan
                {
                    $main_data = PinjamanKaryawan::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->nomor_pinjaman = GenerateNumber::generate_no_pinjaman();
                    $main_data->save();
                }
                if($data_approval->approval_group==14) //Perubahan Masa Cuti
                {

                    $main_data = CutiPerubahan::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();
                    //update perubahan cuti
                    $cuti = Leave::find($main_data->id_head);
                    $cuti->tgl_akhir = $main_data->tgl_akhir_edit;
                    $cuti->jumlah_hari = $main_data->jumlah_hari_edit;
                    $cuti->update();

                }
                if($data_approval->approval_group==15) //Pengajuan Resign
                {
                    $main_data = Resign::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==16) //Pengajuan Exit Interviews
                {
                    $main_data = ExitInterviews::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==17) //Pengajuan penonaktifan SP
                {
                    $main_data = SPNonAktif::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->sts_pengajuan = 2; //Approved
                    $main_data->save();

                    $data_sp = WarningLetter::find($main_data->id_sp);
                    $data_sp->sp_akhir_active = date('Y-m-d');
                    $data_sp->save();
                    //update sp aktive karyawan
                    $id_karyawan = $data_sp->id_karyawan;
                    $current_active = Karyawan::find($id_karyawan);
                    $current_active->sp_active = NULL;
                    $current_active->sp_level_active = NULL;
                    $current_active->sp_lama_active = NULL;
                    $current_active->sp_mulai_active = NULL;
                    $current_active->sp_akhir_active = NULL;
                    $current_active->save();
                }
                if($data_approval->approval_group==18) //Pengajuan THR
                {
                    $main_data = Thr::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
                if($data_approval->approval_group==19) //Pengajuan KPI
                {
                    $main_data = KPIPeriodik::where('approval_key', $data_approval->approval_key)->first();
                    $main_data->status = "closed";
                    $main_data->status_pengajuan = 2; //Approved
                    $main_data->save();
                }
            }
        } elseif($request->pil_persetujuan==3) {
            //if pending
            if($data_approval->approval_group==2) //Aplikasi Pelamar
            {
                $main_data = Pelamar::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_app = "pending"; //Rejected
                $main_data->save();
            }
        } else {
            //update Approval (Reject)
            if($data_approval->approval_group==1) //permintaan tenaga kerja
            {
                $main_data = RecruitmentPengajuanTK::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==2) //Aplikasi Pelamar
            {
                $main_data = Pelamar::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==3) //Cuti
            {
                $main_data = Leave::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==4) //Izin
            {
                $main_data = Permission::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==5) //Aplikasi Perubahan Status
            {
                $main_data = PerubahanStatus::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==6) //Aplikasi Mutasi
            {
                $main_data = Mutasi::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==7) //lembur
            {
                $main_data = Overtime::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==8) //Perjalanan Dinas
            {
                $main_data = Perdis::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==9) //Pengajuan Pelatihan
            {
                $main_data = PengajuanPelatihanHeader::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
                 //update pelatihan header
                $all_pilihan_pelatihan = PengajuanPelatihanHeader::select([
                    'hrd_pengajuan_pelatihan_d.id_pelatihan'
                        ])
                        ->leftJoin('hrd_pengajuan_pelatihan_d', 'hrd_pengajuan_pelatihan_d.id_head', '=', 'hrd_pengajuan_pelatihan_h.id')
                        ->where('hrd_pengajuan_pelatihan_h.approval_key', $data_approval->approval_key)->get();
                foreach($all_pilihan_pelatihan as $item)
                {
                    $update_head = TrainingHead::find($item->id_pelatihan);
                    $update_head->status_pelatihan = 3; //Rejected
                    $update_head->update();
                }

            }

            if($data_approval->approval_group==10) //Pengajuan Surat Teguran
            {
                $main_data = ReprimandLetter::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==11) //Pengajuan Surat Peringatan
            {
                $main_data = WarningLetter::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==12) //Pengajuan Penggajian
            {
                $main_data = PayrollHeader::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==13) //Pengajuan Pinjaman Karyawan
            {
                $main_data = PinjamanKaryawan::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->aktif = 'n'; //tidak aktif
                $main_data->save();
            }
            if($data_approval->approval_group==14) //Perubahan Masa Cuti
            {
                $main_data = CutiPerubahan::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==15) //Perubahan Resign
            {
                $main_data = Resign::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==16) //Perubahan Exit Interviews
            {
                $main_data = ExitInterviews::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==17) //Penonaktifan SP
            {
                $main_data = SPNonAktif::where('approval_key', $data_approval->approval_key)->first();
                $main_data->sts_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==18) //Pengajuan THR
            {
                $main_data = Thr::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
            if($data_approval->approval_group==19) //Pengajuan KPI
            {
                $main_data = KPIPeriodik::where('approval_key', $data_approval->approval_key)->first();
                $main_data->status = "rejected"; //Rejected
                $main_data->status_pengajuan = 3; //Rejected
                $main_data->save();
            }
        }

        return redirect()->route('approval.index')->with('konfirm', 'Data persetujuan berhasil disimpan');
    }

    public function get_nomor_surat_sp()
    {
        $thn = date('Y');
        $bln =  hrdfunction::get_bulan_romawi(date('m'));
        $no_urut = 1;
        $ket_surat = "SSB/SP";
        $nomor_awal = $ket_surat."/".$bln."/".$thn;
        $result = WarningLetter::whereNotNull('no_sp')->orderBy('tgl_sp', 'desc')->first();
        if(empty($result->no_surat))
        {
            $nomor_urut = sprintf('%03s', $no_urut);
        } else {
            $nomor_urut_terakhir = substr($result->no_sp, 0, 3)+1;
            $nomor_urut = sprintf('%03s', $nomor_urut_terakhir);
        }
        return $nomor_urut."/".$nomor_awal;
    }
}
