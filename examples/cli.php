<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;

/**
 * initialize MethodBus core
 */
$bus = new MethodBus();

/**
 * request payload
 */
$payload = [
    'a' => 10,
    'b' => 20
];

/**
 * replay protection headers
 */
$timestamp = (string) time();

$nonce = bin2hex(
    random_bytes(16)
);

/**
 * generate secure HMAC signature
 */
function sign(
    array $payload,
    string $timestamp,
    string $nonce
): string {

    /**
     * normalize payload
     */
    ksort($payload);

    /**
     * build signature source
     */
    $message =
        json_encode($payload)
        . $timestamp
        . $nonce;

    /**
     * generate HMAC SHA256
     */
    return hash_hmac(
        'sha256',
        $message,
        'super-secret-signature-key'
    );
}

/**
 * request context
 */
$context = [
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

        'X-Request-ID' => 'cli-default-001'
    ]
];

/**
 * execute plugin
 */
$result = $bus->call(
    'math:add:v1',
    $payload,
    $context
);

/**
 * output response
 */
print_r($result);