<?php

namespace MethodBus\Runtime;

final class ExecutionContext
{
    public function __construct(
        public string $requestId,
        public string $method,
        public array $payload,
        public array $meta = []
    ) {}
}