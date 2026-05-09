<?php

namespace MethodBus\Core;

final class Response
{
    public static function success(array $data, string $requestId): array
    {
        $app = Config::get('app', []);

        $response = [
            'status' => 'success',
            'request_id' => $requestId,
        ];

        if (!empty($app['debug'])) {
            $response['copyright'] = [
                'debug' => $app['debug'],
                'name' => $app['name'],
                'version' => $app['version']
            ];
        }

        $response['data'] = $data;

        return $response;
    }

    public static function error(string $message, string $requestId, int $code = 500): array
    {
        $app = Config::get('app', []);

        $response = [
            'status' => 'error',
            'request_id' => $requestId,
            'message' => $message,
            'code' => $code
        ];

        if (!empty($app['debug'])) {
            $response['copyright'] = [
                'debug' => $app['debug'],
                'name' => $app['name'],
                'version' => $app['version']
            ];
        }

        return $response;
    }
}