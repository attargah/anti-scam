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

    protected $casts = [
        'ip_address' => 'string',
        'expires_at' => 'datetime',
    ];

    public static function findActiveByIp(?string $ip = null)
    {
        $ip = $ip ?? request()->ip();

        return self::query()
            ->where('ip_address', $ip)->first();
    }


    public static function isBlocked(?string $ip = null): bool
    {
        $record = self::findActiveByIp($ip);
        return $record ? $record->isCurrentlyBlocked() : false;
    }

    public function logs() : HasMany
    {
        return $this->hasMany(BlockedIpLog::class);
    }

    public function isPermanentlyBlocked(): bool
    {
        return empty($this->expires_at);
    }

    public function isCurrentlyBlocked(): bool
    {
        return $this->isPermanentlyBlocked() or $this->expires_at->isFuture();
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
