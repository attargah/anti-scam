<?php

use Attargah\AntiScam\Models\BlockedIp;
use Illuminate\Support\Carbon;

it('isBlocked and findActiveByIp return active and unexpired records', function () {
    $now = now();


    BlockedIp::query()->create([
        'ip_address' => '127.0.0.1',
        'expires_at' => $now->copy()->subMinute(),
    ]);


    BlockedIp::query()->create([
        'ip_address' => '10.0.0.1',
        'expires_at' => null,
    ]);

    expect(BlockedIp::findActiveByIp('127.0.0.1'))->toBeNull();
    expect(BlockedIp::isBlocked('127.0.0.1'))->toBeFalse();

    expect(BlockedIp::findActiveByIp('10.0.0.1'))->not()->toBeNull();
    expect(BlockedIp::isBlocked('10.0.0.1'))->toBeTrue();

    expect(BlockedIp::findActiveByIp('8.8.8.8'))->toBeNull();
    expect(BlockedIp::isBlocked('8.8.8.8'))->toBeFalse();
});

it('isPermanentlyBlocked and isCurrentlyBlocked work correctly', function () {
    $permanent = BlockedIp::query()->create([
        'ip_address' => '1.1.1.1',
        'expires_at' => null,
    ]);

    $temporary = BlockedIp::query()->create([
        'ip_address' => '2.2.2.2',
        'expires_at' => now()->addMinutes(5),
    ]);

    $expired = BlockedIp::query()->create([
        'ip_address' => '3.3.3.3',
        'expires_at' => now()->subMinutes(5),
    ]);

    expect($permanent->isPermanentlyBlocked())->toBeTrue();
    expect($permanent->isCurrentlyBlocked())->toBeTrue();

    expect($temporary->isPermanentlyBlocked())->toBeFalse();
    expect($temporary->isCurrentlyBlocked())->toBeTrue();

    expect($expired->isCurrentlyBlocked())->toBeFalse();
});

it('sets empty string expires_at to null during saving', function () {
    $model = new BlockedIp([
        'ip_address' => '4.4.4.4',
        'expires_at' => '',
    ]);

    $model->save();

    expect($model->expires_at)->toBeNull();
});
