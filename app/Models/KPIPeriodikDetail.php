<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIPeriodikDetail extends Model
{
    use HasFactory;
    protected $table = "hrd_kpi_periodik_detail";
    protected $fillable = [
        'id_head',
        'id_kpi',
        'nama_kpi',
        'tipe',
        'satuan',
        'bobot',
        'target',
        'realisasi',
        'skor_akhir',
        'nilai_kpi'
    ];

    public function getKPIPeriodik()
    {
        return $this->belongsTo(KPIPeriodik::class, 'id_head', 'id');
    }

    public function getKPIMaster()
    {
        return $this->belongsTo(KPIMaster::class, 'id_kpi', 'id');
    }
}
