<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;
use MethodBus\Core\Http;

$bus = new MethodBus(); // inisialisasi core engine MethodBus

/**
 * =========================
 * REQUIRED HEADERS (POSTMAN)
 * =========================
 *
 * Content-Type   : application/json
 * X-Method       : namespace:action:version (contoh: math:add:v1)
 * X-ApiKey       : secret-key-123
 * X-Signature    : HMAC SHA256 dari PRE-REQUEST (contoh: {{signature}})
 * X-Request-ID   : optional tracking id
 *
 * =========================
 * BODY
 * =========================
 *
 * {
 *   "a": 10,
 *   "b": 20
 * }
 *
 * =========================
 * POSTMAN PRE-REQUEST SCRIPT (SIGNATURE)
 * =========================
 *
 * const secret = "super-secret-signature-key";
 *
 * let body = pm.request.body.raw;
 * let json = JSON.parse(body);
 *
 * let sorted = {};
 * Object.keys(json).sort().forEach(k => sorted[k] = json[k]);
 *
 * const signature = CryptoJS.HmacSHA256(
 *     JSON.stringify(sorted),
 *     secret
 * ).toString();
 *
 * pm.environment.set("signature", signature);
 */

$http = new Http($bus);

/**
 * menjalankan HTTP gateway MethodBus
 */
$http
    ->endpoint('/api')         // prefix endpoint (optional)
    ->methodHeader('X-Method') // routing header plugin
    ->handle();                // eksekusi request lifecycle