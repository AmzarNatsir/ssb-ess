<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerdisFasilitas extends Model
{
    protected $table = 'hrd_perdis_fasilitas';

    protected $fillable = [
        'id_perdis', 'id_fasilitas', 'biaya', 'keterangan', 'file_1', 'file_2', 'realisasi'
    ];

    public function perdis(): BelongsTo
    {
        return $this->belongsTo(Perdis::class, 'id_perdis');
    }

    public function masterFasilitas(): BelongsTo
    {
        return $this->belongsTo(FasilitasPerdis::class, 'id_fasilitas');
    }
}
