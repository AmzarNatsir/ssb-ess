<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoInternal extends Model
{
    protected $table = 'hrd_memo_internal';

    protected $casts = [
        'tgl_post' => 'date',
    ];

    public function getFileUrlAttribute()
    {
        if (!$this->file_memo) {
            return null;
        }

        return route('media.proxy', ['path' => $this->file_memo]);
    }
}
