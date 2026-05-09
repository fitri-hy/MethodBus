<?php

namespace MethodBus\Runtime;

final class Lifecycle
{
    public function before(array $payload): array
    {
        return $payload;
    }

    public function after(array $result): array
    {
        return $result;
    }
}