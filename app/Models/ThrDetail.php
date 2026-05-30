<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThrDetail extends Model
{
    use HasFactory;
    protected $table = "hrd_thr_detail";
    protected $fillable = [
        'id_head',
        "id_karyawan",
        'id_departemen',
        "bulan",
        "tahun",
        "gaji_pokok",
        "tunj_tetap",
    ];
}
