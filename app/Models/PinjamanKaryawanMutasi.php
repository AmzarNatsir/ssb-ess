<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKaryawanMutasi extends Model
{
    protected $table = 'hrd_pinjaman_karyawan_mutasi';

    protected $fillable = [
        'id_head',
        'tanggal',
        'nominal',
        'status',
        'bayar_aktif',
    ];
}
