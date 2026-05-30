<?php

namespace App\Traits;

use App\Helpers\HrdFunction;
use App\Models\Leave;
use App\Models\PinjamanKaryawan;

trait GenerateNumber
{
    public static function generate_no_surat()
    {
        $thn = date('Y');
        $bln =  HrdFunction::get_bulan_romawi(date('m'));
        $no_urut = 1;
        $ket_surat = "SC/SSB";
        $nomor_awal = $ket_surat."/".$bln."/".$thn;
        $result = Leave::where('sts_pengajuan', 2)->whereYear('tgl_pengajuan', $thn)->orderBy('id', 'desc')->first();
        if(empty($result->nomor_surat))
        {
            $nomor_urut = sprintf('%03s', $no_urut);
        } else {
            $nomor_urut_terakhir = substr($result->nomor_surat, 0, 3)+1;
            $nomor_urut = sprintf('%03s', $nomor_urut_terakhir);
        }
        return $nomor_urut."/".$nomor_awal;
    }

    public static function generate_no_pinjaman()
    {
        $thn = date('Y');
        $bln =  date('m');
        $no_urut = 1;
        $nomor_awal = $bln.$thn;
        $result = PinjamanKaryawan::where('status_pengajuan', 2)->whereYear('tgl_pengajuan', $thn)->orderBy('id', 'desc')->first();
        if(empty($result->nomor_pinjaman))
        {
            $nomor_urut = sprintf('%05s', $no_urut);
        } else {
            $nomor_urut_terakhir = substr($result->nomor_pinjaman, 0, 5)+1;
            $nomor_urut = sprintf('%05s', $nomor_urut_terakhir);
        }
        return $nomor_urut.$nomor_awal;
    }
}
