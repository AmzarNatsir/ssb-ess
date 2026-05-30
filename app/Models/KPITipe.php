<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPITipe extends Model
{
    protected $table = "mst_hrd_kpi_tipe";
    protected $fillable = [
        'tipe_kpi',
        'active'
    ];
}
