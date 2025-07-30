<?php

use Aboutnima\TelegramAction\Services\TelegramActionService;

beforeEach(function (): void {
    // Create `TelegramActionService` instance
    $this->telegramAction = TelegramActionService::default();
});

it('`TelegramAction` facade is correctly bound and returns an instance of TelegramActionService', function (): void {
    expect($this->telegramAction)->toBeInstanceOf(TelegramActionService::class);
});
