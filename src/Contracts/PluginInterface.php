<?php

namespace MethodBus\Contracts;

interface PluginInterface
{
    public static function namespace(): string;
    public static function action(): string;
    public static function version(): string;
    public static function method(): string;
    public function handle(array $payload): array;
}