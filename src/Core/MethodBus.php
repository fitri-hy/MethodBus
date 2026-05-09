<?php

namespace MethodBus\Core;

use MethodBus\Core\Config;

final class MethodBus
{
    private Kernel $kernel;

    public function __construct()
    {
        Config::load(__DIR__ . '/../../');
        $this->kernel = new Kernel();
    }

    public function call(string $method, array $payload = [], array $context = []): array
    {
        try {
            return $this->kernel->execute($method, $payload, $context);
        } catch (\Throwable $e) {

            return [
                'status' => 'error',
                'request_id' => $context['headers']['X-Request-ID']
                    ?? bin2hex(random_bytes(8)),
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ];
        }
    }

    public function kernel(): Kernel
    {
        return $this->kernel;
    }
}