<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTraining extends Model
{
    protected $table = "mst_hrd_pelatihan";
    protected $fillable = ['nama_pelatihan'];
}
