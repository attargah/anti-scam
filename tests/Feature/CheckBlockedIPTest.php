<?php

use Illuminate\Support\Facades\Route;
use Attargah\AntiScam\Http\Middleware\CheckBlockedIP;
use Attargah\AntiScam\Models\BlockedIp;

beforeEach(function () {
    Route::any('/check-ip-test', function () {
        return response('ok');
    })->middleware(CheckBlockedIP::class);
});

it('allows request for a non-blocked IP', function () {
    $this->get('/check-ip-test')->assertOk();
});

it('returns 403 for a blocked IP', function () {
    BlockedIp::query()->create([
        'ip_address' => '127.0.0.1',
        'expires_at' => null,
    ]);

    $this->get('/check-ip-test')->assertStatus(403);
});
