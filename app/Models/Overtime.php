<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $table = 'hrd_lembur';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_karyawan',
        'tgl_pengajuan',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'deskripsi_pekerjaan',
        'file_surat_perintah_lembur',
        'status_pengajuan',
        'approval_key',
        'current_approval_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_pengajuan' => 'date',
    ];

    /**
     * Get the approvals for the overtime.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approval_key', 'approval_key');
    }

    /**
     * Get the current approver for the overtime.
     */
    public function currentApproval(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    /**
     * Check if the overtime request is locked (i.e., at least one approver has processed it).
     */
    public function isLockedByApprover(): bool
    {
        return $this->approvals()->where('approval_status', '>', 0)->exists();
    }

    /**
     * Get the karyawan associated with the overtime.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }
}
