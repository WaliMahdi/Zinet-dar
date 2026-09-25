<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'code', 'expires_at'])]
class EmailVerificationCode extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Determine whether this code has passed its expiry time.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * The user that owns this verification code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
