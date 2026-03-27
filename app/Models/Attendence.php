<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    protected $table = "hrd_absensi";
    protected $fillable = [
        'id_departemen',
        'id_finger',
        'tanggal',
        'jam',
        'status',
        'lokasi_id',
        'user_id',
        'dhuhur', //y atau t
        'ashar', // y atau t
        'nik_lama'
    ];

}
