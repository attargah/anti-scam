<?php

namespace Attargah\AntiScam;

use Attargah\AntiScam\Resources\BlockedIpLogs\BlockedIpLogResource;
use Attargah\AntiScam\Resources\BlockedIps\BlockedIpResource;
use Attargah\AntiScam\Resources\ScamIps\ScamIpResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

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
