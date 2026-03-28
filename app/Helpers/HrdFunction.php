<?php
namespace App\Helpers;
use App\Models\RefApprovalDetail;
use App\Models\ProfilPerusahaan;

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
        if($result) {
            if(!empty($result->logo_perusahaan)) {
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
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $nama_bulan[$bulan] ?? '';
    }
}