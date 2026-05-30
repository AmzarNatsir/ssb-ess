<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIMaster extends Model
{
    use HasFactory;
    protected $table = "mst_hrd_kpi";
    protected $fillable = [
        'id_departemen',
        'id_tipe',
        'id_satuan',
        'nama_kpi',
        'formula_hitung',
        'data_pendukung',
        'bobot_kpi'
    ];

    public function tipeKPI()
    {
        return $this->belongsTo(KPITipe::class, 'id_tipe', 'id');
    }

    public function satuanKPI()
    {
        return $this->belongsTo(KPISatuan::class, 'id_satuan', 'id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id');
    }
}
