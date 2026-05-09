<?php

namespace MethodBus\Security;

use MethodBus\Exceptions\UnauthorizedException;

final class AuthGuard
{
    public function process(array $payload): array
    {
        // stateless demo guard (production: JWT/HMAC)
        if (($payload['_token'] ?? null) !== 'secret') {
            throw new UnauthorizedException("Invalid token");
        }

        return $payload;
    }
}