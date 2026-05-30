<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisDokumenKaryawan extends Model
{
    protected $table = "mst_hrd_jenis_dokumen_karyawan";
    protected $fillable = ['nm_dokumen', 'status', 'karyawan', 'pelamar'];
}
