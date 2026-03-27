<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $table = 'hrd_cuti';

    /**
     * The attributes that are mass assignable.
     */
     protected $fillable = ['id_karyawan', 'id_jenis_cuti', 'tgl_awal', 'tgl_akhir', 'tgl_masuk', 'jumlah_hari', 'ket_cuti', 'id_user', 'tgl_pengajuan', 'sts_pengajuan', 'id_persetujuan', 'sts_persetujuan', 'tgl_persetujuan', 'ket_persetujuan', 'id_atasan', 'tgl_appr_atasan', 'sts_appr_atasan', 'ket_appr_atasan', 'id_pengganti', 'id_departemen', 'approval_key', 'current_approval_id', 'is_draft', 'jumlah_quota', 'quota_terpakai', 'sisa_quota', 'nomor_surat', 'tanggal_surat'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tgl_awal' => 'date',
        'tgl_akhir' => 'date',
        'tgl_pengajuan' => 'date',
    ];

    /**
     * Get the karyawan associated with the leave.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    /**
     * Get the jenis cuti associated with the leave.
     */
    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class, 'id_jenis_cuti', 'id');
    }

    public function profil_karyawan(){
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function get_jenis_cuti()
    {
        return $this->belongsTo(JenisCuti::class, 'id_jenis_cuti', 'id');
    }

    public function get_karyawan_pengganti()
    {
        return $this->belongsTo(Karyawan::class, 'id_pengganti', 'id');
    }

    public function get_profil_atasan_langsung()
    {
        return $this->belongsTo(Karyawan::class, 'id_atasan', 'id');
    }

    public function get_profil_hrd()
    {
        return $this->belongsTo(Karyawan::class, 'id_persetujuan', 'id');
    }

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    /**
     * Get the approvals associated with the leave request.
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approval_key', 'approval_key')->orderBy('approval_level', 'asc');
    }
}
