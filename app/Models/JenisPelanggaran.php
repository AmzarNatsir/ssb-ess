<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'mst_hrd_jenis_pelanggaran';

    protected $fillable = ['nm_jenis_pelanggaran', 'status'];
}
