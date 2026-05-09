<?php

namespace MethodBus\Middleware;

use MethodBus\Core\Config;
use MethodBus\Core\Request;
use MethodBus\Contracts\MiddlewareInterface;
use MethodBus\Exceptions\UnauthorizedException;

final class AuthMiddleware implements MiddlewareInterface
{
	public function process(Request $request, array $payload): array
	{
		$config = Config::get('security.auth');

		if (!($config['enabled'] ?? false)) {
			return $payload;
		}

		$apiKey = $request->header('X-ApiKey');

		if (!$apiKey || $apiKey !== $config['apikey']) {
			throw new UnauthorizedException("Invalid API Key");
		}

		return $payload;
	}
}