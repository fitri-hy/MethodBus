<?php

namespace MethodBus\Contracts;

interface PluginInterface
{
    public function namespace(): string;

    public function action(): string;

    public function version(): string;

    public function handle(array $payload): array;

    public function method(): string;
}