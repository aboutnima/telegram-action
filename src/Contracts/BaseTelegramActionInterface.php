<?php

namespace Aboutnima\Telegram\Contracts;

/**
 * Interface for all Telegram actions.
 * Ensures consistency across custom action classes.
 */
interface BaseTelegramActionInterface
{
    /**
     * Resolve an instance of the action.
     */
    public static function make(): self;

    /**
     * Execute any logic before the action is handled.
     */
    public function prepare(): void;

    /**
     * Handle the action and return Telegram response.
     */
    public function handle(): mixed;

    /**
     * Get the action key (with payload reference if available).
     */
    public function getKey(): string;

    /**
     * Set the payload received from the previous action.
     */
    public function setPayload(array $payload): void;

    /**
     * Get the payload assigned to this action.
     */
    public function getPayload(): array;

    /**
     * Attach payload to this action and return an updated key.
     */
    public function withPayload(array $payload): self;

    /**
     * Get the chat ID for the current user.
     */
    public function getChatId(): int;

    /**
     * Whether the message should be deleted before the next action.
     */
    public function getDeleteOnNextAction(): bool;

    /**
     * Get the message text to send.
     */
    public function getMessage(): string;

    /**
     * Get the inline keyboard markup for the message.
     */
    public function getInlineKeyboardMarkup(): array;

    /**
     * Get the reply keyboard markup for the message.
     */
    public function getReplyKeyboardMarkup(): array;

    /**
     * Generate the final keyboard markup to attach to the message.
     */
    public function generateReplyMarkup(): mixed;
}
