<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPelatihanDetail extends Model
{
    protected $table = "hrd_pengajuan_pelatihan_d";
    protected $fillable = [
        'id_head',
        'id_pelatihan'
    ];

    public function getPelatihan()
    {
        return $this->belongsTo(TrainingHead::class, 'id_pelatihan', 'id');
    }
}
