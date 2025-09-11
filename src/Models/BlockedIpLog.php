<?php

namespace Attargah\AntiScam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedIpLog extends Model
{
    protected $table = 'blocked_ip_logs';
    protected $fillable = [
        'ip_address',
        'reason',
        'expires_time',
        'expires_at',
        'form_identity',
        'user_agent',
        'request_url',
        'request_path',
        'request_method',
        'blocked_by',
        'blocked_ip_id'
    ];

    public static function getLastExpiresTime(?string $ip)
    {
        return self::query()->where('ip_address', $ip)->orderBy('expires_time', 'desc')->first()->expires_time ?? null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function blockedIp(): BelongsTo
    {
        return $this->belongsTo(BlockedIp::class);
    }
}
