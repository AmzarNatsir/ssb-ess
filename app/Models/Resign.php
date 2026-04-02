<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resign extends Model
{
    protected $table = "hrd_resign";
    protected $fillable = [
        'id_karyawan',
        'tgl_eff_resign',
        'alasan_resign',
        'approval_key',
        'create_by',
        'current_approval_id',
        'is_draft',
        'sts_pengajuan',
        'cara_keluar',
        'nomor_skk',
        'tgl_skk',
        'file_surat_resign',
        'created_at',
        'updated_at'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }
    public function current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function exitInterview()
    {
        return $this->hasOne(ExitInterviews::class, 'id_head', 'id');
    }
}
