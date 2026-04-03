<?php
namespace App\Helpers;
use App\Models\RefApprovalDetail;
use App\Models\ProfilPerusahaan;
use App\Models\PinjamanKaryawanMutasi;

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
}