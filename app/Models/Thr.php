<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thr extends Model
{
    use HasFactory;
     protected $table = "hrd_thr";
    protected $fillable = [
        "bulan",
        "tahun",
        "approval_key",
        "status_pengajuan",
        "current_approval_id",
        "is_draft",
        "diajukan_oleh",
    ];

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }

    public function get_diajukan_oleh(){
        return $this->belongsTo(User::class, 'diajukan_oleh', 'id');
    }
}
