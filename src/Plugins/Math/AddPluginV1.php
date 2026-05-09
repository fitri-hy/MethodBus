<?php

namespace MethodBus\Plugins\Math;

use MethodBus\Contracts\PluginInterface;
use MethodBus\Contracts\PluginDocumentedInterface;

final class AddPluginV1 implements PluginInterface, PluginDocumentedInterface
{
    public function namespace(): string
    {
        return 'math';
    }

    public function action(): string
    {
        return 'add';
    }

    public function version(): string
    {
        return 'v1';
    }

    public function method(): string
    {
        return $this->namespace() . ':' . $this->action() . ':' . $this->version();
    }

    public function handle(array $payload): array
    {
        return [
            'result' => $payload['a'] + $payload['b']
        ];
    }

    public function rules(): array
    {
        return [
            'a' => 'required|numeric',
            'b' => 'required|numeric'
        ];
    }

    public function docs(): array
    {
        return [
            'x-method' => $this->method(),
            'namespace' => $this->namespace(),
            'action' => $this->action(),
            'version' => $this->version(),
            'method' => 'POST',

            'description' => 'Menjumlahkan dua angka',

            'request' => [
                'a' => ['type' => 'number', 'required' => true],
                'b' => ['type' => 'number', 'required' => true]
            ],

            'response' => [
                'result' => ['type' => 'number']
            ]
        ];
    }
}