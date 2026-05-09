<?php

namespace MethodBus\Contracts;

use MethodBus\Core\Request;

interface MiddlewareInterface
{
    public function process(Request $request, array $payload): array;
}