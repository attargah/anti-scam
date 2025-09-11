<?php

use Attargah\AntiScam\Rules\Xss;

it('passes for clean text', function () {
    $rule = new Xss();
    $failed = false;
    $rule->validate('message', 'Hello world', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('fails for text containing HTML or scripts', function () {
    $rule = new Xss();
    $failed = false;
    $rule->validate('message', '<b>Hello</b>', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});
