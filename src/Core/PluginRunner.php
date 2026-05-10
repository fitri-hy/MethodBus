<?php

namespace MethodBus\Core;

use MethodBus\Contracts\PluginInterface;

final class PluginRunner
{
    public function __construct(
        private Container $container
    ) {}

    public function run(string $pluginClass, array $payload): array
    {
        $plugin = $this->container->make($pluginClass);

        if (!$plugin instanceof PluginInterface) {
            throw new \RuntimeException(
                "Invalid plugin [$pluginClass]"
            );
        }

        return $plugin->handle($payload);
    }
}