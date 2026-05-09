<?php

namespace MethodBus\Exceptions;

class ValidationException extends ApiException
{
    protected int $httpCode = 422;

    public function __construct(string $message = "Validation Error")
    {
        parent::__construct($message, 422);
    }
}