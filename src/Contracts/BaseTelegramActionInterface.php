<?php

namespace Aboutnima\Telegram\Contracts;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * Interface that defines the contract for a Telegram action handler.
 */
interface BaseTelegramActionInterface
{
    /**
     * Resolve an instance of the action via the service container.
     */
    public static function make(): self;

    /**
     * Get the unique key identifying this action.
     */
    public function getKey(): string;

    /**
     * Get the chat ID associated with this action.
     */
    public function getChatId(): int;

    /**
     * Get deleteOnNextAction value
     */
    public function getDeleteOnNextAction(): bool;

    /**
     * Set the current payload.
     */
    public function setPayload(array $payload): void;

    /**
     * Get the current payload.
     */
    public function getPayload(): array;

    /**
     * Make action with payload
     */
    public function withPayload(array $payload): self;

    /**
     * Get the message text to send to the Telegram bot.
     * Return null if no message should be sent.
     */
    public function message(): string;

    /**
     * Get the inline keyboard markup
     * Override this method to customize the inline keyboard markup.
     */
    public function inlineKeyboardMarkup(): array;

    /**
     * Get the reply keyboard markup
     * Override this method to customize the reply keyboard markup.
     */
    public function replyKeyboardMarkup(): array;

    /**
     * Generate the final keyboard markup based on reply and inline keyboards.
     * Prefers reply markup if available; falls back to inline markup otherwise.
     */
    public function generateReplyMarkup(): mixed;

    /**
     * Handle the action's logic when invoked.
     */
    public function handle(): mixed;
}
