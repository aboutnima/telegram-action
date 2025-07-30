<?php

namespace Aboutnima\Telegram\Contracts;

interface TelegramActionInterface
{
    /**
     * Get the unique key of the action.
     */
    public function key(): string;

    /**
     * Get the message text for the Telegram bot to send.
     */
    public function text(): string;

    /**
     * Handle the incoming Telegram update.
     */
    public function handle(mixed $update): void;
}
