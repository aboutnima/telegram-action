<?php

declare(strict_types=1);

namespace Aboutnima\Telegram\Services;

final readonly class TelegramActionService
{
    public function default(): self
    {
        return $this;
    }

    public function getChatId(): int
    {
        return 0;
    }

    public function handleRequest($update): void
    {

    }
}
