<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelamarOrganisasi extends Model
{
    protected $table = "hrd_recr_organisasi";
    protected $fillable = ["id_pelamar", "nama_organisasi", "posisi", "mulai_tahun", "sampai_tahun"];
}
