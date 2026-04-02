<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKaryawan extends Model
{
    protected $table = 'hrd_pinjaman_karyawan';

    protected $fillable = [
        'id_karyawan',
        'tgl_pengajuan',
        'kategori',
        'alasan_pengajuan',
        'nominal_apply',
        'nominal_acc',
        'tenor_apply',
        'tenor_acc',
        'angsuran',
        'status_pengajuan',
        'aktif',
        'approval_key',
        'current_approval_id',
        'is_draft',
        'nomor_pinjaman'
    ];

    public function mutasi()
    {
        return $this->hasMany(PinjamanKaryawanMutasi::class, 'id_head', 'id');
    }

    public function dokumen()
    {
        return $this->hasMany(PinjamanKaryawanDokumen::class, 'id_head', 'id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PinjamanKaryawanPembayaran::class, 'id_head', 'id');
    }

    public function currentApprover()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function getKategoriLabelAttribute()
    {
        return $this->kategori == 1 ? 'Panjar Gaji' : 'Pinjaman Kesejahteraan Karyawan (PKK)';
    }

    public function getStatusLabelAttribute()
    {
        $map = [
            1 => 'Pengajuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
            4 => 'Dibatalkan',
        ];
        return $map[$this->status_pengajuan] ?? 'Unknown';
    }
}
