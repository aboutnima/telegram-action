<?php

namespace Aboutnima\Telegram\Contracts;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * Interface that defines the contract for a Telegram action handler.
 */
interface BaseTelegramActionInterface
{
    /**
     * Get the unique key identifying this action.
     */
    public function getKey(): string;

    /**
     * Set payload for next action
     */
    public function setPayload(array $payload): void;

    /**
     * Retrieve key and set payload
     */
    public function getKeyAndSetPayload(array $payload): string;

    /**
     * Set the chat ID for this action.
     */
    public function setChatId(int $chatId): void;

    /**
     * Get the chat ID associated with this action.
     */
    public function getChatId(): int;

    /**
     * Get deleteOnNextAction value
     */
    public function getDeleteOnNextAction(): bool;

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

    /**
     * Handle the action's logic when invoked.
     */
    public function handle(): mixed;
}
