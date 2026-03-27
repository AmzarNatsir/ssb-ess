<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'hrd_karyawan';
    protected $primaryKey = 'id';

    /**
     * Get the position of the employee.
     */
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id');
    }

    public function subDepartemen(): BelongsTo
    {
        return $this->belongsTo(SubDepartemen::class, 'id_subdepartemen', 'id');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id');
    }

    /**
     * Calculate length of service.
     */
    public function getLamaBekerjaAttribute()
    {
        if (!$this->tgl_masuk) return '-';

        $joinDate = \Carbon\Carbon::parse($this->tgl_masuk);
        $diff = $joinDate->diff(\Carbon\Carbon::now());

        return "{$diff->y} Year - {$diff->m} Month - {$diff->d} Day";
    }
}
