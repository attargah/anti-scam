<?php

use Illuminate\Support\Facades\Route;
use Attargah\AntiScam\Http\Middleware\XSSProtection;

beforeEach(function () {
    Route::post('/xss-protection-test', function () {
        return response()->json(request()->all());
    })->middleware(XSSProtection::class);
});

it('sanitizes HTML and scripts from string inputs', function () {
    $payload = [
        'name' => '<b>John</b> <script>alert(1)</script>',
        'nested' => [
            'field' => '<img src=x onerror=alert(1)>',
        ],
    ];

    $response = $this->postJson('/xss-protection-test', $payload);

    $response->assertOk();
    $response->assertJson([
        'name' => 'John alert(1)',
        'nested' => [
            'field' => '',
        ],
    ]);
});
