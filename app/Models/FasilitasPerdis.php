<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasPerdis extends Model
{
    protected $table = 'mst_hrd_fasilitas_perdis';

    protected $fillable = [
        'nm_fasilitas', 'status'
    ];
}
