<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\TestResponse;
use Attargah\AntiScam\Http\Middleware\AntiSpam;
use Attargah\AntiScam\Models\BlockedIp;
use Attargah\AntiScam\Models\BlockedIpLog;

beforeEach(function () {
    Cache::flush();

    config()->set('anti-scam.spam.active', true);
    config()->set('anti-scam.spam.max_requests_per_window', 2);
    config()->set('anti-scam.spam.window_in_seconds', 60);
    config()->set('anti-scam.spam.ban_duration_multiplier', 3);
    config()->set('anti-scam.spam.permanent_ban_threshold_min', 10080);

    Route::any('/anti-spam-test', function () {
        return response('ok');
    })->middleware(AntiSpam::class);
});

it('allows requests below the threshold', function () {
    $first = $this->get('/anti-spam-test');
    $second = $this->get('/anti-spam-test');

    $first->assertOk();
    $second->assertOk();
});

it('returns 403 and logs IP when threshold is exceeded', function () {
    $this->get('/anti-spam-test')->assertOk();
    $this->get('/anti-spam-test')->assertOk();

    $response = $this->get('/anti-spam-test');
    $response->assertStatus(403);

    $ip = '127.0.0.1';

    expect(BlockedIp::query()->where('ip_address', $ip)->exists())->toBeTrue();
    expect(BlockedIpLog::query()->where('ip_address', $ip)->exists())->toBeTrue();
});
