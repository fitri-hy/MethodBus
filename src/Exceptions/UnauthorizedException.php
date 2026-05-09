<?php

namespace MethodBus\Exceptions;

class UnauthorizedException extends ApiException
{
    protected int $httpCode = 401;

    public function __construct(string $message = "Unauthorized")
    {
        parent::__construct($message, 401);
    }
}