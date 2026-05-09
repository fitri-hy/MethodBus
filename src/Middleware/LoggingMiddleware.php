<?php

namespace MethodBus\Middleware;

use MethodBus\Contracts\MiddlewareInterface;

final class LoggingMiddleware implements MiddlewareInterface
{
    public function process(array $payload): array
    {
        return $payload;
    }
}