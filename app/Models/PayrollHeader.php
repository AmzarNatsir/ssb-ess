<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollHeader extends Model
{
    protected $table = "hrd_payroll_header";
    protected $fillable = [
        'bulan',
        'tahun',
        'approval_key',
        'status_pengajuan',
        'current_approval_id',
        'is_draft',
        'diajukan_oleh'
    ];

    public function get_current_approve()
    {
        return $this->belongsTo('App\Models\Karyawan', 'current_approval_id', 'id');
    }

    public function get_diajukan_oleh(){
        return $this->belongsTo('App\Models\User', 'diajukan_oleh', 'id');
    }
}
