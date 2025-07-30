<?php

namespace Aboutnima\Telegram\Actions;

use Aboutnima\Telegram\Contracts\TelegramActionInterface;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Base class for all Telegram actions.
 * Provides default implementations for message sending settings.
 */
abstract class BaseTelegramAction implements TelegramActionInterface
{
    /**
     * Telegram chat ID to send the message to.
     */
    private int $chatId;

    /**
     * Set the chat ID.
     */
    public function setChatId(int $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * Get the current chat ID.
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * Get the message text to send.
     * Override this method to customize the message.
     */
    public function message(): ?string
    {
        return null;
    }

    /**
     * Get the reply markup (e.g., inline keyboard).
     * Override this method to customize the markup.
     */
    public function replyMarkup(): ?array
    {
        return null;
    }

    /**
     * Handle the action: send the message and markup to Telegram.
     */
    public function handle(): void
    {
        $payload = [
            'chat_id' => $this->getChatId(),
            'text' => $this->message(),
        ];

        if (($markup = $this->replyMarkup()) !== null && ($markup = $this->replyMarkup()) !== []) {
            $payload['reply_markup'] = $markup;
        }

        Telegram::sendMessage($payload);
    }
}
