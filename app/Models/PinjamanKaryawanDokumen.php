<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKaryawanDokumen extends Model
{
    protected $table = 'hrd_pinjaman_karyawan_dokumen';

    protected $fillable = [
        'id_head',
        'file_dokumen',
        'keterangan',
    ];
}
