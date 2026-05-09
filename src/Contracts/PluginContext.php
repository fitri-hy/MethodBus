<?php

namespace MethodBus\Contracts;

final class PluginContext
{
    public function __construct(
        public readonly array $payload
    ) {}
}