<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefApprovalDetail extends Model
{
    use HasFactory;
    protected $table = "ref_approval_detail";
    protected $guarded = [];

    public function getPejabat()
    {
        return $this->belongsTo(Karyawan::class, 'approval_by_employee', 'id');
    }
}
