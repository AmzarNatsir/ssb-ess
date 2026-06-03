<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'token_hash', 'is_active', 'expires_at', 'last_used_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Generate a new plain token, return both plain and hash.
     * Caller is responsible for saving the model.
     */
    public static function generate(string $name, ?\Carbon\Carbon $expiresAt = null): array
    {
        $plain = bin2hex(random_bytes(32)); // 64 hex chars
        $hash  = hash_hmac('sha256', $plain, config('app.key'));

        return [
            'plain' => $plain,
            'model' => self::create([
                'name'       => $name,
                'token'      => $plain,
                'token_hash' => $hash,
                'is_active'  => true,
                'expires_at' => $expiresAt,
            ]),
        ];
    }

    /**
     * Find a token record by plain token value.
     */
    public static function findByPlain(string $plain): ?self
    {
        $hash = hash_hmac('sha256', $plain, config('app.key'));

        return self::where('token_hash', $hash)
            ->where('is_active', true)
            ->first();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
