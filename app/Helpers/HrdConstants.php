<?php

namespace App\Helpers;

use App\Models\RefApprovalDetail;

class HrdConstants
{
    public const AGAMA = [
        "1" => "Islam",
        "2" => "Kristen Katolik",
        "3" => "Kristen Protestan",
        "4" => "Hindu",
        "5" => "Budha",
        "6" => "Kong Hu Cu"
    ];

    public const JENJANG_PENDIDIKAN = [
        "1" => "TK/Play Group",
        "2" => "Sekolah Dasar (SD)",
        "3" => "Sekolah Menengah Pertama (SMP) / Sederajat",
        "4" => "Sekolah Menengah Atas (SMA) / Sederajat",
        "5" => "Diploma Tiga (D3)",
        "6" => "Strata Satu (S1)",
        "7" => "Strata Dua (S2)",
        "8" => "Strata Tiga (S3)"
    ];

    public const STATUS_PERNIKAHAN = [
        "1" => "Menikah",
        "2" => "Belum Menikah",
        "3" => "Duda",
        "4" => "Janda"
    ];

    public const HUBUNGAN_LBKELUARGA = [
        "1" => "Ayah",
        "2" => "Ibu",
        "3" => "Saudara"
    ];

    public const HUBUNGAN_KELUARGA = [
        "1" => "Suami",
        "2" => "Istri",
        "3" => "Anak"
    ];

    public const STATUS_KARYAWAN = [
        "1" => "Training",
        "2" => "Kontrak",
        "3" => "Tetap",
        "4" => "Resign",
        "5" => "PHK",
        "6" => "Pensiun",
        "7" => "Harian"
    ];

    public const STATUS_CUTI = [
        "1" => "Pengajuan",
        "2" => "Disetujui",
        "3" => "Ditolak",
        "4" => "Dibatalkan"
    ];

    public const STATUS_IZIN = [
        "1" => "Pengajuan",
        "2" => "Disetujui",
        "3" => "Ditolak",
        "4" => "Dibatalkan"
    ];

    public const MONTHS = [
        "1" => "Januari",
        "2" => "Februari",
        "3" => "Maret",
        "4" => "April",
        "5" => "Mei",
        "6" => "Juni",
        "7" => "Juli",
        "8" => "Agustus",
        "9" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember"
    ];
}
