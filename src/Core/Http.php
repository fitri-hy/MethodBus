<?php

namespace MethodBus\Core;

final class Http
{
    private string $methodHeader = 'HTTP_X_METHOD';
    private string $endpointPrefix = '';

    public function __construct(
        private MethodBus $bus
    ) {}

    public function methodHeader(string $header): self
    {
        $this->methodHeader = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return $this;
    }

    public function endpoint(string $prefix): self
    {
        $this->endpointPrefix = $prefix;
        return $this;
    }

    public function handle(): void
    {
        header('Content-Type: application/json');

        $rawMethod = $_SERVER[$this->methodHeader] ?? null;
        $requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? bin2hex(random_bytes(8));

        $payload = json_decode(file_get_contents("php://input"), true);
        if (!is_array($payload)) $payload = [];

        if (!$rawMethod) {
            $this->responseError($requestId, 'Method header missing', 400);
        }

        $parts = explode(':', $rawMethod);

        if (count($parts) !== 3) {
            $this->responseError($requestId, 'Invalid method format (namespace:action:version)', 400);
        }

        [$namespace, $action, $version] = $parts;

        if ($namespace === 'system' && $action === 'docs') {

            try {
                $pluginManager = $this->bus->kernel()->getPluginManager();
                $generator = new OpenApiGenerator($pluginManager);

                $this->response(
                    Response::success(
                        [
                            'version' => $version,
                            'docs' => $generator->generate($version)
                        ],
                        $requestId
                    )
                );

            } catch (\Throwable $e) {
                $this->responseError($requestId, $e->getMessage(), 500);
            }
        }

        $method = $namespace . ':' . $action . ':' . $version;

        $response = $this->bus->call($method, $payload, [
            'headers' => array_change_key_case(getallheaders(), CASE_UPPER),
            'server'  => $_SERVER
        ]);

        if (!isset($response['request_id'])) {
            $response['request_id'] = $requestId;
        }

        $this->response($response);
    }

    private function response(array $data): void
    {
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    private function responseError(string $requestId, string $message, int $code): void
    {
        $this->response(
            Response::error($message, $requestId, $code)
        );
    }
}