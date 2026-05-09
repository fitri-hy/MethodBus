<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;

$bus = new MethodBus(); // inisialisasi core engine MethodBus (plugin + middleware + kernel)

/**
 * payload request
 * data input yang akan diproses oleh plugin math:add:v1
 */
$payload = [
    'a' => 10,
    'b' => 20
];

/**
 * generate signature (WAJIB)
 * digunakan untuk validasi keamanan server (anti manipulasi payload)
 */
function sign(array $payload): string
{
    ksort($payload); // pastikan urutan data konsisten agar signature tidak berubah

    return hash_hmac(
        'sha256',
        json_encode($payload), // payload harus sama dengan server
        'super-secret-signature-key'
    );
}

/**
 * request context (WAJIB untuk auth + signature validation)
 * semua header dikirim manual di CLI mode
 */
$context = [
    'headers' => [
        'Content-Type' => 'application/json', // format request
        'X-ApiKey'     => 'secret-key-123',   // autentikasi API key
        'X-Signature'  => sign($payload),     // signature HMAC payload
        'X-Request-ID' => 'cli-default-001'   // tracking request
    ]
];

/**
 * eksekusi plugin MethodBus
 * format: namespace:action:version
 */
$result = $bus->call(
    'math:add:v1',
    $payload,
    $context
);

/**
 * output response dari server
 */
print_r($result);