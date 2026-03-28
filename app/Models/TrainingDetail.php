<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingDetail extends Model
{
    protected $table = "hrd_pelatihan_d";
    protected $fillable = [
        'id_head',
        'id_karyawan',
        'biaya_investasi',
        'tujuan_pelatihan_pasca',
        'uraian_materi_pasca',
        'tindak_lanjut_pasca',
        'dampak_pasca',
        'penutup_pasca',
        'evidence_pasca',
        'pasca'
    ];

    public function get_karyawan()
    {
        return $this->belongsTo('App\Models\Karyawan', 'id_karyawan', 'id');
    }
}
