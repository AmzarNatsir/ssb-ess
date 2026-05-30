<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelamarPengalamanKerja extends Model
{
    protected $table = "hrd_recr_pengalaman_kerja";
    protected $fillable = ["id_pelamar", "nm_perusahaan", "posisi", "alamat", "mulai_tahun", "sampai_tahun"];
}
