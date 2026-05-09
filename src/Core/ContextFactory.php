<?php

namespace MethodBus\Core;

use MethodBus\Contracts\PluginContext;

final class ContextFactory
{
    public static function make(array $payload): PluginContext
    {
        return new PluginContext($payload);
    }
}