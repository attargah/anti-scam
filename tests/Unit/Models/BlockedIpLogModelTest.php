<?php

use Attargah\AntiScam\Models\BlockedIpLog;

it('getLastExpiresTime returns the latest expires_time value', function () {
    $ip = '127.0.0.1';

    BlockedIpLog::query()->create([
        'ip_address' => $ip,
        'expires_time' => 1,
    ]);

    BlockedIpLog::query()->create([
        'ip_address' => $ip,
        'expires_time' => 5,
    ]);

    BlockedIpLog::query()->create([
        'ip_address' => $ip,
        'expires_time' => 3,
    ]);

    expect(BlockedIpLog::getLastExpiresTime($ip))->toBe('5');
});
