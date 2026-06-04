<?php

namespace MethodBus\Security;

use MethodBus\Core\Config;
use MethodBus\Core\Request;
use RuntimeException;

final class SignatureVerifier
{
    public function process(
        Request $request,
        array $payload
    ): array {

        $config = Config::get(
            'security.signature'
        );

        if (!($config['enabled'] ?? false)) {
            return $payload;
        }

        $signature = $request->header(
            'X-Signature'
        );

        if (!$signature) {
            throw new RuntimeException(
                'Missing signature'
            );
        }

        ksort($payload);

        $timestamp =
            $request->header(
                'X-Timestamp'
            ) ?? '';

        $nonce =
            $request->header(
                'X-Nonce'
            ) ?? '';

        $secret =
            $config['secret']
            ?? '';

        $message =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
            . $timestamp
            . $nonce;

        $expected = hash_hmac(
            'sha256',
            $message,
            $secret
        );

        if (
            !hash_equals(
                $expected,
                $signature
            )
        ) {
            throw new RuntimeException(
                'Invalid signature'
            );
        }

        return $payload;
    }
}
