<?php

namespace MethodBus\Middleware;

use MethodBus\Core\Request;
use MethodBus\Contracts\MiddlewareInterface;
use MethodBus\Exceptions\ValidationException;

final class ValidationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, array $payload): array
    {
        if (!is_array($payload)) {
            throw new ValidationException("Payload must be array");
        }

        if (empty($payload)) {
            throw new ValidationException("Payload cannot be empty");
        }

        foreach ($payload as $key => $value) {

            if (is_string($value) && strlen($value) > 10000) {
                throw new ValidationException("Payload value too large for key '{$key}'");
            }

            if (is_array($value) && count($value) > 1000) {
                throw new ValidationException("Payload structure too deep for key '{$key}'");
            }
        }

        return $payload;
    }
}