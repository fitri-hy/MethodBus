<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;

/**
 * generate secure HMAC signature
 */
function sign(
    array $payload,
    string $timestamp,
    string $nonce
): string {

    ksort($payload);

    $message =
        json_encode($payload)
        . $timestamp
        . $nonce;

    return hash_hmac(
        'sha256',
        $message,
        'super-secret-signature-key'
    );
}

/**
 * reusable request context builder
 */
function makeContext(
    string $requestId,
    array $payload
): array {

    $timestamp = (string) time();

    $nonce = bin2hex(
        random_bytes(16)
    );

    return [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-ApiKey' => 'secret-key-123',
            'X-Timestamp' => $timestamp,
            'X-Nonce' => $nonce,
            'X-Signature' => sign(
                $payload,
                $timestamp,
                $nonce
            ),

            'X-Request-ID' => $requestId
        ]
    ];
}

/**
 * initialize MethodBus
 */
$bus = new MethodBus();

/**
 * payload request
 */
$payload = [
    'a' => 15,
    'b' => 25
];

/**
 * execute plugin
 */
$result = $bus->call(
    'math:add:v1',
    $payload,
    makeContext(
        'cli-custom-002',
        $payload
    )
);

/**
 * output response
 */
print_r($result);