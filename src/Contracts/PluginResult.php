<?php

namespace MethodBus\Contracts;

final class PluginResult
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $data,
        public readonly ?string $error = null
    ) {}
}