<?php

namespace Attargah\AntiScam\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockedIp extends Model
{
    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip_address',
        'expires_at'
    ];

    public static function findActiveByIp(?string $ip = null): ?self
    {
        $ip = $ip ?? request()->ip();

        return self::query()
            ->where('ip_address', $ip)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }


    public static function isBlocked(?string $ip = null): bool
    {
        return (bool) self::findActiveByIp($ip);
    }

    public function logs() : HasMany
    {
        return $this->hasMany(BlockedIpLog::class);
    }

    public function isPermanentlyBlocked(): bool
    {
        return is_null($this->expires_at);
    }

    public function isCurrentlyBlocked(): bool
    {
        return $this->isPermanentlyBlocked() || $this->expires_at->isFuture();
    }

    protected static function booted()
    {
        static::saving(function ($blockedIp) {
            if ($blockedIp->expires_at === '') {
                $blockedIp->expires_at = null;
            }
        });
    }

}
