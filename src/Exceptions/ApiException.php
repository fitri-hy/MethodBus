<?php

namespace MethodBus\Exceptions;

class ApiException extends \Exception
{
    protected int $httpCode = 400;

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}