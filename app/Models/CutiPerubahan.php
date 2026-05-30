<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiPerubahan extends Model
{
    protected $table = "hrd_cuti_perubahan";
    protected $fillable = ['id_head', 'tgl_akhir_edit', 'jumlah_hari_edit', 'alasan_perubahan', 'tgl_awal_origin', 'tgl_akhir_origin', 'jumlah_hari_origin', 'approval_key', 'create_by', 'current_approval_id', 'is_draft', 'sts_pengajuan'];

    public function get_cuti_origin(){
        return $this->belongsTo(Leave::class, 'id_head', 'id');
    }

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function get_create_by()
    {
        return $this->belongsTo(User::class, 'create_by', 'id');
    }
}
