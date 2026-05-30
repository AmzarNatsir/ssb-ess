<?php
namespace App\Helpers;

use App\Models\Approval;
use App\Models\MstJenisSp;
use App\Models\RefApprovalDetail;
use App\Models\ProfilPerusahaan;
use App\Models\PinjamanKaryawanMutasi;
use DateTime;

class HrdFunction
{
    public static function set_approval_cek($group, $dept)
    {
        $queryCheck = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->get()->count();
        return $queryCheck;
    }

    public static function set_approval_get_first($group, $dept)
    {
        $getFirst = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->orderBy('approval_level', 'asc')->first()->approval_by_employee;
        return $getFirst;
    }

    public static function set_approval_new($group, $dept)
    {
        $queryAll = RefApprovalDetail::where('approval_group', $group)->where('approval_departemen', $dept)->get();
        return $queryAll;
    }

    public static function get_kop_surat()
    {
        $result = ProfilPerusahaan::first();
        $logo = "";
        if ($result) {
            if (!empty($result->logo_perusahaan)) {
                $logo = $result->logo_perusahaan;
            }
        }
        $data = [
            'alamat_situs' => "https://pt-ssb.co.id",
            'lokasi' => "POMALAA - KOLAKA - SULAWESI TENGGARA - INDONESIA",
            'logo' => "assets/logo_perusahaan/logo_ssb.png"
        ];
        return $data;
    }

    public static function get_nama_bulan($bulan)
    {
        $nama_bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        return $nama_bulan[$bulan] ?? '';
    }

    public static function generate_duedate_pinjaman_karyawan($id_head, $tgl_bayar)
    {
        $arr_tgl_bayar = explode("-", $tgl_bayar);
        $tgl_1 = $arr_tgl_bayar[2];
        $bln_1 = $arr_tgl_bayar[1];
        $thn_1 = $arr_tgl_bayar[0];

        if ($bln_1 == 12) {
            $bln_baru = "01";
            $thn_baru = $thn_1 + 1;
        } else {
            $bln_baru = $bln_1 + 1;
            $thn_baru = $thn_1;
        }
        $tgl_jatuh_tempo = $thn_baru . "-" . $bln_baru . "-" . $tgl_1;

        $resData = PinjamanKaryawanMutasi::where('id_head', $id_head)->where('bayar_aktif', 1)->first();
        PinjamanKaryawanMutasi::find($resData->id)->update([
            "tanggal" => $tgl_jatuh_tempo
        ]);
    }

    public static function terbilang($nilai)
    {
        $angka = abs((int) $nilai);
        $huruf = [
            "",
            "Satu",
            "Dua",
            "Tiga",
            "Empat",
            "Lima",
            "Enam",
            "Tujuh",
            "Delapan",
            "Sembilan",
            "Sepuluh",
            "Sebelas"
        ];

        $temp = "";
        if ($angka < 12) {
            $temp = " " . $huruf[$angka];
        } else if ($angka < 20) {
            $temp = self::terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $temp = self::terbilang(intval($angka / 10)) . " Puluh " . self::terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = " Seratus " . self::terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = self::terbilang(intval($angka / 100)) . " Ratus " . self::terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = " Seribu " . self::terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = self::terbilang(intval($angka / 1000)) . " Ribu " . self::terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = self::terbilang(intval($angka / 1000000)) . " Juta " . self::terbilang($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $temp = self::terbilang(intval($angka / 1000000000)) . " Miliar " . self::terbilang($angka % 1000000000);
        } else if ($angka < 1000000000000000) {
            $temp = self::terbilang(intval($angka / 1000000000000)) . " Triliun " . self::terbilang($angka % 1000000000000);
        }

        return trim($temp);
    }

    public static function get_bulan_romawi($bln)
    {
        switch($bln) {
            case '01' :
                $bln_huruf = "I";
            break;
            case '02' :
                $bln_huruf = "II";
            break;
            case '03' :
                $bln_huruf = "III";
            break;
            case '04' :
                $bln_huruf = "IV";
            break;
            case '05' :
                $bln_huruf = "V";
            break;
            case '06' :
                $bln_huruf = "VI";
            break;
            case '07' :
                $bln_huruf = "VII";
            break;
            case '08' :
                $bln_huruf = "VIII";
            break;
            case '09' :
                $bln_huruf = "IX";
            break;
            case '10' :
                $bln_huruf = "X";
            break;
            case '11' :
                $bln_huruf = "XI";
            break;
            case '12' :
                $bln_huruf = "XII";
            break;
            default:
                $bln_huruf = "Tidak di ketahui";
            break;
        }
        return $bln_huruf;
    }

    public static function get_lama_kerja_karyawan($tgl_mulai)
    {
        $waktuMulai = new DateTime($tgl_mulai);
        $waktuSelesai = new Datetime(date('Y-m-d'));

        $selisihWaktu = $waktuMulai->diff($waktuSelesai);

        return $selisihWaktu->format('%Y tahun, %m bulan, %d hari');
    }

    public static function get_masa_berlaku_sp($jenis_sp)
    {
        $master = MstJenisSp::find($jenis_sp);
        $masa_berlaku = ($master->lama_berlaku); //6 bulan
        return $masa_berlaku;
    }

    public static function get_notif_approval()
    {
        $id_user = auth()->user()->karyawan->id;
        $queryNotif = Approval::where('approval_active', 1)->where('approval_by_employee', $id_user)->get();

        $tot_1 = Approval::where('approval_group', 1)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_1_ket = "Pengajuan Permintaan Tenaga Kerja";
        $tot_2 = Approval::where('approval_group', 2)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_2_ket = "Pengajuan Aplikasi Pelamar";
        $tot_3 = Approval::where('approval_group', 3)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_3_ket = "Pengajuan Cuti";
        $tot_4 = Approval::where('approval_group', 4)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_4_ket = "Pengajuan Izin";
        $tot_5 = Approval::where('approval_group', 5)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_5_ket = "Pengajuan Perubahan Status";
        $tot_6 = Approval::where('approval_group', 6)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_6_ket = "Pengajuan Mutasi";
        $tot_7 = Approval::where('approval_group', 7)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_7_ket = "Pengajuan Lembur";
        $tot_8 = Approval::where('approval_group', 8)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_8_ket = "Pengajuan Perjalanan Dinas";
        $tot_9 = Approval::where('approval_group', 9)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_9_ket = "Pengajuan Pelatihan";
        $tot_10 = Approval::where('approval_group', 10)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_10_ket = "Pengajuan Surat Teguran";
        $tot_11 = Approval::where('approval_group', 11)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_11_ket = "Pengajuan Surat Peringatan";
        $tot_12 = Approval::where('approval_group', 12)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_12_ket = "Pengajuan Penggajian";
        $tot_13 = Approval::where('approval_group', 13)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_13_ket = "Pengajuan Pinjaman Karyawan";
        $tot_14 = Approval::where('approval_group', 14)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_14_ket = "Pengajuan Perubahan Masa Cuti";
        $tot_15 = Approval::where('approval_group', 15)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_15_ket = "Pengajuan Resign";
        $tot_16 = Approval::where('approval_group', 16)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_16_ket = "Pengajuan Form Exit Interviews";
        $tot_17 = Approval::where('approval_group', 17)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_17_ket = "Pengajuan Penonaktifan Surat Peringatan";
        $tot_18 = Approval::where('approval_group', 18)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_18_ket = "Pengajuan Tunjangan Hari Raya";
        $tot_19 = Approval::where('approval_group', 19)->where('approval_active', 1)->where('approval_by_employee', $id_user)->get()->count();
        $tot_19_ket = "Penilaian KPI Departemen";

        $total_appr = $queryNotif->count();

        $html = '<a href="#" class="search-toggle iq-waves-effect">
        <i class="ri-notification-2-line"></i>
        <span class="badge badge-pill badge-danger badge-up count-mail">'.$total_appr.'</span>
        </a>
        <div class="iq-sub-dropdown">
            <div class="iq-card shadow-none m-0">
                <div class="iq-card-body p-0 ">
                    <div class="bg-danger p-3">
                        <h5 class="mb-0 text-white">All Notifications<small
                                class="badge  badge-light float-right pt-1">'.$total_appr.'</small></h5>
                    </div>';
        if($total_appr > 0) {
        $html .= '<a href='.url("hrd/persetujuan").' class="iq-sub-card" >
                <div class="media align-items-center">
                <div class="media-body ml-3">';
                    if($tot_1 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_1_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_1.'</small></h6><hr>';
                    }
                    if($tot_2 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_2_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_2.'</small></h6><hr>';
                    }
                    if($tot_3 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_3_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_3.'</small></h6><hr>';
                    }
                    if($tot_4 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_4_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_4.'</small></h6><hr>';
                    }
                    if($tot_5 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_5_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_5.'</small></h6><hr>';
                    }
                    if($tot_6 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_6_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_6.'</small></h6><hr>';
                    }
                    if($tot_7 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_7_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_7.'</small></h6><hr>';
                    }
                    if($tot_8 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_8_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_8.'</small></h6><hr>';
                    }
                    if($tot_9 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_9_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_9.'</small></h6><hr>';
                    }
                    if($tot_10 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_10_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_10.'</small></h6><hr>';
                    }
                    if($tot_11 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_11_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_11.'</small></h6><hr>';
                    }
                    if($tot_12> 0) {
                        $html .= '<h6 class="mb-0">'.$tot_12_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_12.'</small></h6><hr>';
                    }
                    if($tot_13 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_13_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_13.'</small></h6><hr>';
                    }
                    if($tot_14 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_14_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_14.'</small></h6><hr>';
                    }
                    if($tot_15 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_15_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_15.'</small></h6><hr>';
                    }
                    if($tot_16 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_16_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_16.'</small></h6><hr>';
                    }
                    if($tot_17 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_17_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_17.'</small></h6><hr>';
                    }
                    if($tot_18 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_18_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_18.'</small></h6><hr>';
                    }
                    if($tot_19 > 0) {
                        $html .= '<h6 class="mb-0">'.$tot_19_ket.'<small class="badge  badge-light float-right pt-1">'.$tot_19.'</small></h6><hr>';
                    }
        $html .= '</div>
                </div>
            </a>';
        }
        $html .='</div>
            </div>
        </div>';
        echo $html;
    }
}
