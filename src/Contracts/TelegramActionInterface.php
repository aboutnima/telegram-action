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
     * */
    public function message(): string|null;

    /**
     * Get the reply markup for the Telegram bot to send.
     */
    public function replyMarkup(): array|null;

    /**
     * Handle action
     */
    public function handle(): void;
}
