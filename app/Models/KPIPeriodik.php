<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIPeriodik extends Model
{
    use HasFactory;
    protected $table = "hrd_kpi_periodik";
    protected $fillable = [
        'id_departemen',
        'bulan',
        'tahun',
        'status',
        'total_kpi',
        'user_created',
        'submit_at',
        'user_submit',
        'approval_key',
        'status_pengajuan',
        'current_approval_id',
        'is_draft',
        'diajukan_oleh'
    ];

    public function getDepartemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id');
    }

    public function get_diajukan_oleh(){
        return $this->belongsTo(User::class, 'diajukan_oleh', 'id');
    }
}
