<?php

namespace MethodBus\Loader;

use MethodBus\Core\PluginManager;
use MethodBus\Contracts\PluginInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

final class PluginLoader
{
    public function __construct(
        private PluginManager $pluginManager,
        private string $basePath = __DIR__ . '/../Plugins'
    ) {}

    public function load(): void
    {
        foreach ($this->scanFiles($this->basePath) as $file) {

            require_once $file;

            $class = $this->getClassFromFile($file);

            if (!$class || !class_exists($class)) {
                continue;
            }

            $this->registerIfPlugin($class);
        }
    }

    private function scanFiles(string $path): array
    {
        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {

            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }

    private function getClassFromFile(string $file): ?string
    {
        $content = file_get_contents($file);

        $namespace = null;
        $class = null;

        if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
            $namespace = $m[1];
        }

        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $content, $m)) {
            $class = $m[1];
        }

        if (!$class) {
            return null;
        }

        return $namespace ? $namespace . '\\' . $class : $class;
    }

    private function registerIfPlugin(string $class): void
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->implementsInterface(PluginInterface::class)) {
            return;
        }

        if ($reflection->isAbstract()) {
            return;
        }

        $instance = $reflection->newInstance();

        $this->pluginManager->register($instance);
    }
}