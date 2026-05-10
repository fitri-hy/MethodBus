<?php

namespace MethodBus\Core;

final class Request
{
    private string $requestId;

    public function __construct(
        private array $payload,
        private array $server,
        private array $headers = []
    ) {
        $this->requestId =
            $this->headers['X-Request-ID']
            ?? $this->server['HTTP_X_REQUEST_ID']
            ?? bin2hex(random_bytes(8));
    }

    public static function capture(): self
    {
        return new self(
            json_decode(file_get_contents("php://input"), true) ?? [],
            $_SERVER,
            self::extractHeaders($_SERVER)
        );
    }

    public static function fromContext(array $payload, array $context = []): self
    {
        return new self(
            $payload,
            $context['server'] ?? [],
            $context['headers'] ?? []
        );
    }

    private static function extractHeaders(array $server): array
    {
        $headers = [];

        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[ucwords($name, '-')]= $value;
            }
        }

        return $headers;
    }

    public function all(): array
    {
        return $this->payload;
    }

    public function header(string $key): ?string
    {
        return $this->headers[$key]
            ?? $this->headers[strtolower($key)]
            ?? $this->server['HTTP_' . strtoupper(str_replace('-', '_', $key))]
            ?? null;
    }

    public function id(): string
    {
        return $this->requestId;
    }

	public function timestamp(): ?string
	{
		return $this->header('X-Timestamp');
	}

	public function nonce(): ?string
	{
		return $this->header('X-Nonce');
	}
}