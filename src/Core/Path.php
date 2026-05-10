<?php

namespace MethodBus\Core;

final class Path
{
    public static function root(
        string $path = ''
    ): string {

        $root = dirname(__DIR__, 2);

        return $path
            ? $root . '/' . ltrim($path, '/')
            : $root;
    }
}