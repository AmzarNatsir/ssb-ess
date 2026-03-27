<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstJenisSp extends Model
{
    protected $table = 'mst_hrd_jenis_sp';

    protected $fillable = ['nm_jenis_sp', 'status'];
}
