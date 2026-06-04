<?php

namespace MethodBus\Security;

use MethodBus\Core\Config;
use MethodBus\Core\Request;
use RuntimeException;

final class ReplayGuard
{
    public function __construct(
        private NonceStore $store
    ) {}

    public function process(
        Request $request,
        array $payload
    ): array {

        $config = Config::get(
            'security.replay'
        );

        if (!($config['enabled'] ?? false)) {
            return $payload;
        }

        $timestamp = $request->header(
            'X-Timestamp'
        );

        $nonce = $request->header(
            'X-Nonce'
        );

        if (!$timestamp || !$nonce) {
            throw new RuntimeException(
                'Missing replay protection headers'
            );
        }

        $ttl = (int) (
            $config['ttl'] ?? 30
        );

        if (
            abs(
                time() - (int) $timestamp
            ) > $ttl
        ) {
            throw new RuntimeException(
                'Request expired'
            );
        }

        $apiKey = $request->header(
            'X-ApiKey'
        ) ?? '';

        $key =
            $apiKey
            . ':'
            . $nonce;

        if (
            !$this->store->remember(
                $key,
                $ttl
            )
        ) {
            throw new RuntimeException(
                'Replay attack detected'
            );
        }

        return $payload;
    }
}
