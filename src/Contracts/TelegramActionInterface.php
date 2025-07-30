<?php

namespace Aboutnima\Telegram\Contracts;

interface TelegramActionInterface
{
    /**
     * Handle the incoming Telegram update.
     */
    public function handle(mixed $update): void;
}
