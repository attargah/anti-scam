<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Attargah\AntiScam\Http\Middleware\AntiScam;
use Attargah\AntiScam\Models\BlockedIp;
use Attargah\AntiScam\Models\BlockedIpLog;
use Attargah\AntiScam\Models\ScamIp;

beforeEach(function () {
    config()->set('anti-scam.scam.active', true);
    config()->set('anti-scam.key', 'secret-key');
    config()->set('anti-scam.scam.save_log', true);
    config()->set('anti-scam.scam.ban', true);

    Route::post('/anti-scam-test', function () {
        return response('ok');
    })->middleware(AntiScam::class);
});

it('allows request with a single correct hidden input', function () {
    $inputs = config('anti-scam.scam.inputs');

    $index = 0;
    $text = config('anti-scam.key').",".$index.",".$inputs[$index]['name'];
    $payload = [
        $inputs[$index]['name'] => Hash::make($text),
        'form_identity' => 'contact-form',
    ];

    $response = $this->post('/anti-scam-test', $payload);

    $response->assertOk();
    expect(ScamIp::query()->count())->toBe(0)
        ->and(BlockedIp::query()->count())->toBe(0)
        ->and(BlockedIpLog::query()->count())->toBe(0);
});

it('records as scam and bans when inputs are invalid or excessive', function () {
    $inputs = config('anti-scam.scam.inputs');

    $text0 = config('anti-scam.key').",0,".$inputs[0]['name'];
    $payload = [
        $inputs[0]['name'] => Hash::make($text0),
        $inputs[1]['name'] => 'not-a-valid-hash',
        'form_identity' => 'contact-form',
    ];

    $response = $this->post('/anti-scam-test', $payload);
    $response->assertOk();

    $ip = '127.0.0.1';

    expect(ScamIp::query()->where('ip_address', $ip)->exists())->toBeTrue()
        ->and(BlockedIp::query()->where('ip_address', $ip)->exists())->toBeTrue()
        ->and(BlockedIpLog::query()->where('ip_address', $ip)->exists())->toBeTrue();
});
