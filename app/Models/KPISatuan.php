<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPISatuan extends Model
{
    protected $table = "mst_hrd_kpi_satuan";
    protected $fillable = [
        'satuan_kpi',
        'active'
    ];
}
