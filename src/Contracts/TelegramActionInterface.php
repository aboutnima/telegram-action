<?php

namespace Aboutnima\Telegram\Contracts;

interface TelegramActionInterface
{
    public function getChatId(): int;

    public function setChatId(int $chatId): void;

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
