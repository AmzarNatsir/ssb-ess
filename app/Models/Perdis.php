<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perdis extends Model
{
    protected $table = 'hrd_perdis';

    protected $fillable = [
        'id_karyawan', 'no_perdis', 'tgl_perdis', 'maksud_tujuan', 'tgl_berangkat', 
        'tgl_kembali', 'id_uangsaku', 'id_fasilitas', 'ket_perdis', 'id_user', 
        'tgl_pengajuan', 'id_persetujuan', 'sts_persetujuan', 'tgl_persetujuan', 
        'ket_persetujuan', 'sts_pengajuan', 'tujuan', 'id_departemen', 
        'diajukan_oleh', 'id_approval_al', 'status_approval_al', 'tanggal_approval_al', 
        'desk_approval_al', 'approval_key', 'current_approval_id', 'is_draft'
    ];

    protected $casts = [
        'tgl_perdis' => 'date',
        'tgl_berangkat' => 'date',
        'tgl_kembali' => 'date',
        'tgl_pengajuan' => 'date',
        'tgl_persetujuan' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function fasilitas(): HasMany
    {
        return $this->hasMany(PerdisFasilitas::class, 'id_perdis');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approval_key', 'approval_key')->orderBy('approval_level', 'asc');
    }
}
