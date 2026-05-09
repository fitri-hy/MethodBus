<?php

namespace MethodBus\Core;

final class Dispatcher
{
    public function __construct(
        private Kernel $kernel
    ) {}

    public function dispatch(string $method, array $payload): array
    {
        return $this->kernel->execute($method, $payload);
    }
}