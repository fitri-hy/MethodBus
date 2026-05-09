<?php

namespace MethodBus\Core;

use MethodBus\Contracts\PluginInterface;

final class PluginRunner
{
    public function run(PluginInterface $plugin, array $payload): array
    {
        return $plugin->handle($payload);
    }
}