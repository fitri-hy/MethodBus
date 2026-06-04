<?php

namespace MethodBus\Security;

interface NonceStore
{
    public function remember(
        string $key,
        int $ttl
    ): bool;
}
