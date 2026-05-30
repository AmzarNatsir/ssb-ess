<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    protected $table = "hrd_recr_pelamar";
    protected $fillable = ['no_identitas', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenkel', 'id_agama', 'alamat', 'no_hp', 'no_wa', 'email', 'file_photo', 'status', 'id_departemen', 'id_sub_departemen', 'id_jabatan', 'id_lowongan', 'status_nikah', 'id_jenjang', 'no_surat_pengantar', 'tgl_surat_pengantar', 'surat_by', 'hrd_by', 'id_karyawan', 'no_surat_si', 'tgl_surat_si', 'id_perubahan_status', 'approval_key', 'status_pengajuan', 'current_approval_id', 'is_draft', 'psikotes_nilai', 'psikotes_ket', 'psikotes_kesimpulan', 'wawancara_nilai', 'wawancara_ket', 'wawancara_kesimpulan', 'total_skor', 'status_app'];
}
