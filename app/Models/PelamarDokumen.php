<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelamarDokumen extends Model
{
    protected $table = "hrd_recr_dokumen";
    protected $fillable = ["id_pelamar", "id_dokumen", "file_dokumen", "path_file"];

    public function get_master_dokumen()
    {
        return $this->belongsTo(JenisDokumenKaryawan::class, "id_dokumen", "id");
    }
}
