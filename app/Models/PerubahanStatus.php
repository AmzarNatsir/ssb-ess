<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PerubahanStatus extends Model
{
    protected $table = "hrd_perubahan_status";
    protected $fillable = ['id_karyawan', 'no_surat', 'tgl_surat', 'tgl_eff_lama', 'tgl_akh_lama', 'id_sts_lama', 'tgl_eff_baru', 'tgl_akh_baru', 'id_sts_baru', 'no_auto', 'id_ttd', 'id_user', 'tgl_pengajuan', 'status_pengajuan', 'diajukan_oleh', 'alasan_pengajuan', 'id_departemen', 'id_persetujuan', 'sts_persetujuan', 'tgl_persetujuan', 'ket_persetujuan', 'id_approval_al', 'status_approval_al', 'tanggal_approval_al', 'desk_approval_al', 'approval_key', 'current_approval_id', 'is_draft', 'file_hasil_evaluasi'];

    public function get_profil()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function get_departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id');
    }

    public function get_diajukan_oleh()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function get_status_karyawan($id)
    {
        return \App\Helpers\HrdConstants::STATUS_KARYAWAN[$id] ?? '';
    }

    public function get_create_by($id)
    {
        // return $this->belongsTo('App\User', 'surat_by', 'id');
       return DB::table('users')->where('users.id', $id)
        ->leftjoin('hrd_karyawan', 'users.nik', '=', 'hrd_karyawan.nik')
        ->leftjoin('mst_hrd_jabatan', 'hrd_karyawan.id_jabatan', '=', 'mst_hrd_jabatan.id')
        ->select('hrd_karyawan.nm_lengkap', 'mst_hrd_jabatan.nm_jabatan')->get()->first();


    }
}
