<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $table = "hrd_approval";
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function get_profil_employee()
    {
        return $this->belongsTo('App\Models\Karyawan', 'approval_by_employee', 'id');
    }
    public function get_ref_approval()
    {
        return $this->belongsTo('App\Models\RefApproval', 'approval_group', 'id');
    }
}
