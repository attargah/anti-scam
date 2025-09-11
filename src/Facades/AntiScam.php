<?php

namespace Attargah\AntiScam\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Attargah\AntiScam\AntiScam
 */
class AntiScam extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Attargah\AntiScam\AntiScam::class;
    }
}
