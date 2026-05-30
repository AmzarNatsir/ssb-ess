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

    public function jabatan_awal(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_awal', 'id');
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

    public function get_jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }

    public function get_departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id');
    }

    public function get_subdepartemen(): BelongsTo
    {
        return $this->belongsTo(SubDepartemen::class, 'id_subdepartemen', 'id');
    }

    public function get_divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id');
    }

    public function get_status_karyawan($id)
    {
        return \App\Helpers\HrdConstants::STATUS_KARYAWAN[$id] ?? '';
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

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('build/img/users/user-40.jpg');
        }

        return route('media.proxy', ['path' => $this->photo, 'type' => 'photo']);
    }

    public function getInitialsAttribute()
    {
        $name = $this->nm_lengkap;
        if (!$name) return '?';

        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper(substr($w, 0, 1));
            }
            if (strlen($initials) >= 2) break;
        }
        return $initials;
    }
}
