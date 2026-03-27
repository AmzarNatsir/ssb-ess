<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarningLetter extends Model
{
    protected $table = 'hrd_sp';

    protected $fillable = [
        'id_karyawan',
        'id_jenis_sp_diajukan',
        'id_jenis_sp_disetujui',
        'no_sp',
        'tgl_sp',
        'pelanggaran',
        'sts_pengajuan',
        // Add other fields as needed based on the table structure
    ];

    protected $casts = [
        'tgl_sp' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }

    public function jenisSpDiajukan(): BelongsTo
    {
        return $this->belongsTo(MstJenisSp::class, 'id_jenis_sp_diajukan', 'id');
    }

    public function jenisSpDisetujui(): BelongsTo
    {
        return $this->belongsTo(MstJenisSp::class, 'id_jenis_sp_disetujui', 'id');
    }
}
