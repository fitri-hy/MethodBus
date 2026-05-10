<?php

namespace MethodBus\Plugins\Math;

use MethodBus\Contracts\PluginInterface;
use MethodBus\Contracts\PluginDocumentedInterface;
use MethodBus\Plugins\Math\Services\MathService;

final class AddPluginV1 implements
    PluginInterface,
    PluginDocumentedInterface
{
    public function __construct(
        private MathService $math
    ) {}

    public static function namespace(): string
    {
        return 'math';
    }

    public static function action(): string
    {
        return 'add';
    }

    public static function version(): string
    {
        return 'v1';
    }

    public static function method(): string
    {
        return sprintf(
            '%s:%s:%s',
            static::namespace(),
            static::action(),
            static::version()
        );
    }

    public function handle(array $payload): array
    {
        return [
            'result' => $this->math->add(
                $payload['a'],
                $payload['b']
            )
        ];
    }

    public function rules(): array
    {
        return [
            'a' => 'required|numeric',
            'b' => 'required|numeric'
        ];
    }

	public static function docs(): array
	{
		return [
			'x-method' => static::method(),

			'namespace' => static::namespace(),

			'action' => static::action(),

			'version' => static::version(),

			'method' => 'POST',

			'description' => 'Menjumlahkan dua angka',

			'request' => [
				'a' => [
					'type' => 'number',
					'required' => true
				],

				'b' => [
					'type' => 'number',
					'required' => true
				]
			],

			'response' => [
				'result' => [
					'type' => 'number'
				]
			]
		];
	}
}