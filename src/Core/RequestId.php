<?php

namespace MethodBus\Core;

final class RequestId
{
    public static function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}