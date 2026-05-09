<?php

namespace MethodBus\Core;

final class Logger
{
    public function log(string $requestId, string $stage, array $data = []): void
    {
        $log = [
            'request_id' => $requestId,
            'stage' => $stage,
            'data' => $data,
            'time' => date('c')
        ];

        file_put_contents(
            __DIR__ . '/../../storage.log',
            json_encode($log) . PHP_EOL,
            FILE_APPEND
        );
    }
}