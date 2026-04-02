<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitInterviews extends Model
{
    protected $table = "hrd_form_exit_interviews";
    protected $fillable = [
        'id_head',
        'jawaban_1',
        'jawaban_1_1',
        'jawaban_1_2',
        'jawaban_1_3',
        'jawaban_2',
        'jawaban_3',
        'jawaban_4',
        'jawaban_5',
        'jawaban_6',
        'jawaban_6_1',
        'jawaban_6_2',
        'jawaban_7',
        'jawaban_8',
        'jawaban_8_1',
        'jawaban_9',
        'jawaban_9_1',
        'jawaban_9_2',
        'jawaban_9_3',
        'jawaban_9_4',
        'jawaban_9_5',
        'jawaban_9_6',
        'jawaban_9_7',
        'jawaban_9_8',
        'jawaban_9_9',
        'jawaban_10',
        'approval_key',
        'create_by',
        'current_approval_id',
        'is_draft',
        'sts_pengajuan',
        'created_at',
        'updated_at'
    ];

    public function getPengajuan()
    {
        return $this->belongsTo(Resign::class, 'id_head', 'id');
    }

    public function get_current_approve()
    {
        return $this->belongsTo(Karyawan::class, 'current_approval_id', 'id');
    }
}
