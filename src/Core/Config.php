<?php

namespace MethodBus\Core;

final class Config
{
    private static array $config = [];

    public static function load(string $basePath): void
    {
        self::$config['app'] = self::safeLoad($basePath . '/config/app.php');
        self::$config['security'] = self::safeLoad($basePath . '/config/security.php');
        self::$config['pipeline'] = self::safeLoad($basePath . '/config/pipeline.php');
    }

    private static function safeLoad(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $data = require $file;

        return is_array($data) ? $data : [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);

        $value = self::$config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public static function all(): array
    {
        return self::$config;
    }
}