<?php

namespace Attargah\AntiScam;

use Attargah\AntiScam\Models\ScamIp;
use Attargah\AntiScam\Resources\BlockedIpLogResource;
use Attargah\AntiScam\Resources\BlockedIpResource;
use Attargah\AntiScam\Resources\ScamIpResource;
use Filament\Contracts\Plugin;
use Illuminate\Support\Facades\Blade;
use Filament\Panel;
use Illuminate\Support\Str;

class AntiScamPlugin implements Plugin
{
    public function getId(): string
    {
        return 'anti-scam';
    }

    public function register(Panel $panel): void
    {

        $panel->resources([
            ScamIpResource::class,
            BlockedIpResource::class,
            BlockedIpLogResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {

    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
