<?php

namespace MethodBus\Security;

interface NonceStore
{
    public function has(string $nonce): bool;

    public function store(string $nonce, int $ttl): void;
}