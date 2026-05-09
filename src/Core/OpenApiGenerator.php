<?php

namespace MethodBus\Core;

use MethodBus\Core\PluginManager;

final class OpenApiGenerator
{
    private PluginManager $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    public function generate(string $version = 'latest'): array
    {
        $grouped = [];

        foreach ($this->pluginManager->all() as $method => $plugin) {

            if (!method_exists($plugin, 'docs')) {
                continue;
            }

            $meta = $plugin->docs();

            $baseKey = $meta['namespace'] . ':' . $meta['action'];

            $grouped[$baseKey][] = $meta;
        }

        $result = [];

        foreach ($grouped as $base => $versions) {

            if ($version === 'latest') {

                $latest = $this->getLatestVersion($versions);
                $result[$latest['x-method']] = $latest;

            } else {

                foreach ($versions as $v) {
                    if (($v['version'] ?? null) === $version) {
                        $result[$v['x-method']] = $v;
                    }
                }
            }
        }

        return $result;
    }

    private function getLatestVersion(array $versions): array
    {
        usort($versions, function ($a, $b) {
            return version_compare(
                $a['version'] ?? 'v1',
                $b['version'] ?? 'v1'
            );
        });

        return end($versions);
    }
}