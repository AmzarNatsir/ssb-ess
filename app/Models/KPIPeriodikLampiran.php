<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIPeriodikLampiran extends Model
{
    use HasFactory;
    protected $table = "hrd_kpi_periodik_lampiran";
    protected $fillable = [
        'id_head',
        'id_detail_kpi',
        'keterangan',
        'file_lampiran'
    ];

    public function kpiPeriodik()
    {
        return $this->belongsTo(KPIPeriodik::class, 'id_head', 'id');
    }
}
