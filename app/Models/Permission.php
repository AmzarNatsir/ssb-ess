<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
    protected $table = 'hrd_izin';

    protected $fillable = ['id_karyawan', 'id_jenis_izin', 'tgl_awal', 'tgl_akhir', 'ket_izin', 'id_user', 'tgl_pengajuan', 'sts_pengajuan', 'id_persetujuan', 'sts_persetujuan', 'tgl_persetujuan', 'ket_persetujuan', 'jumlah_hari', 'id_atasan', 'tgl_appr_atasan', 'sts_appr_atasan', 'ket_appr_atasan', 'id_departemen', 'approval_key', 'status_pengajuan', 'current_approval_id', 'is_draft'];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function jenisPermission(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class, 'id_jenis_izin', 'id');
    }
    /**
     * Get the approvals associated with the permission request.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approval_key', 'approval_key')->orderBy('approval_level', 'asc');
    }

}
