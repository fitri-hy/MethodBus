<?php

namespace MethodBus\Core;

use MethodBus\Contracts\MiddlewareInterface;

final class Pipeline
{
    public function __construct(
        private array $middleware = []
    ) {}

	public function handle(Request $request, array $payload): array
	{
		$data = $payload;

		foreach ($this->middleware as $middleware) {

			if (method_exists($middleware, 'process')) {

				if ($middleware instanceof \MethodBus\Security\SignatureVerifier) {
					$data = $middleware->process($request, $data);
				} else {
					$data = $middleware->process($request, $data);
				}
			}
		}

		return $data;
	}
}