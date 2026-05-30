<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPelatihanHeader extends Model
{
    protected $table = "hrd_pengajuan_pelatihan_h";
    protected $fillable = [
        'tahun',
        'deskripsi',
        'approval_key',
        'status_pengajuan',
        'current_approval_id',
        'is_draft',
        'diajukan_oleh'
    ];

    public function get_detail()
    {
        return $this->hasMany(PengajuanPelatihanDetail::class, 'id_head');
    }

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function get_diajukan_oleh()
    {
        return $this->belongsTo(Karyawan::class, 'diajukan_oleh', 'id');
    }
}
