<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;
use MethodBus\Core\Http;

/**
 * initialize MethodBus core
 */
$bus = new MethodBus();

/**
 * ============================================
 * REQUIRED REQUEST HEADERS
 * ============================================
 *
 * Content-Type
 * application/json
 *
 * X-Method
 * namespace:action:version
 *
 * Example:
 * math:add:v1
 *
 * X-ApiKey
 * API authentication key
 *
 * Example:
 * secret-key-123
 *
 * X-Timestamp
 * unix timestamp
 *
 * Example:
 * 1715342220
 *
 * X-Nonce
 * unique random request id
 *
 * Example:
 * 550e8400e29b41d4a716446655440000
 *
 * X-Signature
 * HMAC SHA256 signature
 *
 * Signature source:
 * json(sorted_payload)
 * + timestamp
 * + nonce
 *
 * X-Request-ID
 * optional request tracing id
 *
 * ============================================
 * REQUEST BODY
 * ============================================
 *
 * {
 *   "a": 10,
 *   "b": 20
 * }
 *
 * ============================================
 * POSTMAN PRE-REQUEST SCRIPT
 * ============================================
 *
 * const secret =
 *     "super-secret-signature-key";
 *
 * const timestamp =
 *     Math.floor(Date.now() / 1000)
 *     .toString();
 *
 * const nonce =
 *     crypto.randomUUID();
 *
 * let body =
 *     JSON.parse(pm.request.body.raw);
 *
 * /**
 *  * normalize payload
 *  *\/
 *
 * let sorted = {};
 *
 * Object.keys(body)
 *     .sort()
 *     .forEach(key => {
 *         sorted[key] = body[key];
 *     });
 *
 * /**
 *  * generate signature
 *  *\/
 *
 * const message =
 *     JSON.stringify(sorted)
 *     + timestamp
 *     + nonce;
 *
 * const signature =
 *     CryptoJS.HmacSHA256(
 *         message,
 *         secret
 *     ).toString();
 *
 * /**
 *  * inject variables
 *  *\/
 *
 * pm.environment.set(
 *     "timestamp",
 *     timestamp
 * );
 *
 * pm.environment.set(
 *     "nonce",
 *     nonce
 * );
 *
 * pm.environment.set(
 *     "signature",
 *     signature
 * );
 *
 * ============================================
 * POSTMAN HEADERS
 * ============================================
 *
 * X-Timestamp : {{timestamp}}
 * X-Nonce     : {{nonce}}
 * X-Signature : {{signature}}
 *
 */

/**
 * initialize HTTP gateway
 */
$http = new Http($bus);

/**
 * execute HTTP lifecycle
 */
$http->handle();