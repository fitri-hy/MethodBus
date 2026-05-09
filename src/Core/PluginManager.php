<?php

namespace MethodBus\Core;

use MethodBus\Contracts\PluginInterface;

final class PluginManager
{
    private array $plugins = [];

	public function register(PluginInterface $plugin): void
	{
		$this->plugins[$plugin->method()] = $plugin;
	}

    public function resolve(string $method): PluginInterface
    {
        if (isset($this->plugins[$method])) {
            return $this->plugins[$method];
        }

        [$ns, $action, $version] = explode(':', $method);

        if ($version === 'latest') {

            $candidates = [];

            foreach ($this->plugins as $key => $plugin) {

                if (str_starts_with($key, $ns . ':' . $action . ':')) {
                    $candidates[] = $key;
                }
            }

            if (!empty($candidates)) {
                rsort($candidates);
                return $this->plugins[$candidates[0]];
            }
        }

        throw new \RuntimeException("Plugin [$method] not found");
    }

    public function all(): array
    {
        return $this->plugins;
    }
}