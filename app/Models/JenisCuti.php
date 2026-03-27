<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    protected $table = 'mst_hrd_jenis_cuti_izin';

    /**
     * Filter by jenis_ci = 1 (Leave)
     */
    public function scopeLeave($query)
    {
        return $query->where('jenis_ci', 1);
    }

    /**
     * Filter by jenis_ci = 2 (Permission)
     */
    public function scopePermission($query)
    {
        return $query->where('jenis_ci', 2);
    }
}
