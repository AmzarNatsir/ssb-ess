<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReprimandLetter extends Model
{
    protected $table = 'hrd_surat_teguran';

    protected $fillable = ['id_karyawan', 'tanggal_kejadian', 'waktu_kejadian', 'tempat_kejadian', 'id_jenis_pelanggaran', 'akibat', 'tindakan', 'rekomendasi', 'komentar_pelanggar', 'tanggal_pengajuan', 'approval_key', 'status_pengajuan', 'current_approval_id', 'is_draft', 'create_by', 'no_st', 'tgl_st', 'sp_lama_active', 'sp_mulai_active', 'sp_akhir_active'];

    protected $casts = [
        'tanggal_kejadian' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'id_jenis_pelanggaran', 'id');
    }

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'create_by', 'id');
    }

    public function currentApprove(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }
}
