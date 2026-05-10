<?php

namespace MethodBus\Core;

use MethodBus\Core\PluginManager;
use MethodBus\Contracts\PluginDocumentedInterface;

final class OpenApiGenerator
{
    public function __construct(
        private PluginManager $pluginManager
    ) {}

    public function generate(
        string $version = 'latest'
    ): array {

        $grouped = [];

        foreach (
            $this->pluginManager->all()
            as $method => $pluginClass
        ) {

            if (!is_subclass_of(
                $pluginClass,
                PluginDocumentedInterface::class
            )) {
                continue;
            }

            $meta = $pluginClass::docs();

            $baseKey =
                $meta['namespace']
                . ':'
                . $meta['action'];

            $grouped[$baseKey][] = $meta;
        }

        $result = [];

        foreach ($grouped as $base => $versions) {

            if ($version === 'latest') {

                $latest = $this->getLatestVersion(
                    $versions
                );

                $result[
                    $latest['x-method']
                ] = $latest;

            } else {

                foreach ($versions as $v) {

                    if (
                        ($v['version'] ?? null)
                        === $version
                    ) {
                        $result[
                            $v['x-method']
                        ] = $v;
                    }
                }
            }
        }

        return $result;
    }

    private function getLatestVersion(
        array $versions
    ): array {

        usort($versions, function ($a, $b) {

            return version_compare(
                $a['version'] ?? 'v1',
                $b['version'] ?? 'v1'
            );
        });

        return end($versions);
    }
}