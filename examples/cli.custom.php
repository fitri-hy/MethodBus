<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;

/**
 * generate signature (WAJIB)
 * digunakan untuk validasi keamanan server
 */
function sign(array $payload): string
{
    ksort($payload); // stabilisasi data sebelum hashing

    return hash_hmac(
        'sha256',
        json_encode($payload),
        'super-secret-signature-key'
    );
}

/**
 * membuat context request
 * reusable builder untuk header CLI
 */
function makeContext(string $requestId, array $payload): array
{
    return [
        'headers' => [
            'Content-Type' => 'application/json', // format request
            'X-ApiKey'     => 'secret-key-123',   // API authentication
            'X-Signature'  => sign($payload),     // signature HMAC
            'X-Request-ID' => $requestId          // tracking request
        ]
    ];
}

$bus = new MethodBus(); // inisialisasi core engine MethodBus

$payload = [
    'a' => 15,
    'b' => 25
];

/**
 * eksekusi plugin MethodBus dengan context reusable
 */
$result = $bus->call(
    'math:add:v1',
    $payload,
    makeContext('cli-custom-002', $payload)
);

/**
 * output response server
 */
print_r($result);