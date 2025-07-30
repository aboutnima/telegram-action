<?php

namespace Aboutnima\Telegram\Facades;

use Illuminate\Support\Facades\Facade;

final class TelegramAction extends Facade
{
    /**
     * Get the registered name of the package.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'telegram-action';
    }
}
