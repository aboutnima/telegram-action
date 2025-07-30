<?php

namespace Aboutnima\Telegram\Contracts;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * Interface that defines the contract for a Telegram action handler.
 */
interface TelegramActionInterface
{
    /**
     * Get the unique key identifying this action.
     */
    public function key(): string;

    /**
     * Handle the action's logic when invoked.
     */
    public function handle(): void;

    /**
     * Get the chat ID associated with this action.
     */
    public function getChatId(): int;

    /**
     * Set the chat ID for this action.
     */
    public function setChatId(int $chatId): void;

    /**
     * Get the message text to send to the Telegram bot.
     * Return null if no message should be sent.
     */
    public function message(): string;

    /**
     * Get the reply markup (e.g., keyboard or inline buttons) to send.
     * Return null if no markup is needed.
     */
    public function replyMarkup(): mixed;
}
