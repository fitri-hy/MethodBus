<?php

namespace MethodBus\Plugins\Math\Services;

final class MathService
{
    public function add(
        int|float $a,
        int|float $b
    ): int|float {
        return $a + $b;
    }
}