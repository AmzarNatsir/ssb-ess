<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKaryawanPembayaran extends Model
{
    protected $table = 'hrd_pinjaman_karyawan_pembayaran';

    protected $fillable = [
        'id_head',
        'tanggal',
        'nominal',
        'id_user',
        'bukti_bayar',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(PinjamanKaryawan::class, 'id_head', 'id');
    }
}
