<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoInternal extends Model
{
    protected $table = 'hrd_memo_internal';

    protected $casts = [
        'tgl_post' => 'date',
    ];
}
