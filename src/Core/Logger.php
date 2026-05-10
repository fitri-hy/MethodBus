<?php

namespace MethodBus\Core;

final class Logger
{
    private string $logFile;

    public function __construct()
    {
        $storagePath = Path::root(
            'storage/methodbus'
        );

        if (!is_dir($storagePath)) {
            mkdir(
                $storagePath,
                0777,
                true
            );
        }

        $this->logFile =
            $storagePath
            . '/logger.log';
    }

    public function log(
        string $requestId,
        string $stage,
        array $data = []
    ): void {

        $log = [
            'request_id' => $requestId,
            'stage' => $stage,
            'data' => $data,
            'time' => date('c')
        ];

        file_put_contents(
            $this->logFile,
            json_encode($log)
            . PHP_EOL,
            FILE_APPEND
        );
    }
}