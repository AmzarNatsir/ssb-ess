<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = "hrd_payroll";
    protected $fillable = ["id_karyawan", "id_departemen", "bulan", "tahun", "gaji_pokok", "tunj_perusahaan", "tunj_tetap", "hours_meter", "bpjsks_karyawan", "bpjstk_jht_karyawan", "bpjstk_jp_karyawan", "bpjstk_jkm_karyawan", "bpjstk_jkk_karyawan", "bpjsks_perusahaan", "bpjstk_jht_perusahaan", "bpjstk_jp_perusahaan", "bpjstk_jkm_perusahaan", "bpjstk_jkk_perusahaan", "cetak_slip", "gaji_bpjs", "gaji_jamsostek", "flag", "appr_date", "appr_by", "lembur", "pot_sedekah", "pot_pkk", "pot_air", "pot_rumah", "pot_toko_alif", "thp", "bonus", "gaji_bruto", "pot_tunj_perusahaan"];

    public function get_profil()
    {
        return $this->belongsTo('App\Models\Karyawan', 'id_karyawan', 'id');
    }
}
