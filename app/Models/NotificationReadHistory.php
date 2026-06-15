<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationReadHistory extends Model
{
    protected $table = 'notification_read_history';

    protected $fillable = [
        'user_id',
        'item_module',
        'item_id',
        'marked_at',
        'marked_all_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
        'marked_all_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
