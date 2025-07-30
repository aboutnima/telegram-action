<?php

declare(strict_types=1);

namespace Aboutnima\LaravelZoom\Services;

use Aboutnima\LaravelZoom\Auth\ZoomTokenManager;
use Aboutnima\LaravelZoom\Contracts\Services\ZoomServiceInterface;
use Aboutnima\LaravelZoom\Exceptions\ZoomException;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final readonly class TelegramActionService
{
    public function __construct() {}
}
