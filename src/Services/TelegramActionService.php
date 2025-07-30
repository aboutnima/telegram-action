<?php

declare(strict_types=1);

namespace Aboutnima\TelegramAction\Services;

use Aboutnima\TelegramAction\Auth\ZoomTokenManager;
use Aboutnima\TelegramAction\Contracts\Services\ZoomServiceInterface;
use Aboutnima\TelegramAction\Exceptions\ZoomException;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final readonly class TelegramActionService
{
    public function __construct() {}
}
