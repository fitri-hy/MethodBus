# MethodBus

**MethodBus** adalah library PHP berbasis *Service Bus Architecture* dengan pendekatan *Single-Endpoint Gateway*.

MethodBus menerima seluruh request melalui satu endpoint terpusat, melakukan validasi keamanan berlapis, lalu mendistribusikan request ke plugin sesuai routing method.

Library ini dirancang untuk:

* API Gateway
* Internal Service Communication
* Modular Monolith
* Plugin-Based Application
* Microservice Architecture
* Service Oriented Architecture (SOA)

---

## Fitur Utama

* Single Endpoint Gateway
* Header-Based Routing
* Plugin Versioning
* Dependency Injection Container
* Constructor Injection
* Middleware Pipeline
* Lazy Plugin Resolution
* Replay Attack Protection
* Timestamp Validation
* Nonce Validation
* HMAC SHA256 Signature
* HTTP & CLI Support
* Auto Plugin Discovery
* Versioned API Documentation

---

## Arsitektur Inti

| Komponen          | Fungsi                         |
| ----------------- | ------------------------------ |
| HTTP Gateway      | Gerbang utama request HTTP     |
| Kernel            | Orchestrator lifecycle request |
| Pipeline          | Middleware execution chain     |
| Plugin Manager    | Registry metadata plugin       |
| Container         | Dependency Injection resolver  |
| Plugin Runner     | Menjalankan plugin runtime     |
| OpenApi Generator | Generator dokumentasi otomatis |

---

## Struktur Konfigurasi

MethodBus menggunakan konfigurasi berbasis file `config/security.php`:

```php
<?php

return [
    'auth' => [
        'enabled' => true,
        'apikey' => 'secret-key-123'
    ],

    'signature' => [
        'enabled' => true,
        'secret' => 'super-secret-signature-key'
    ],
    'replay' => [
        'enabled' => true,
        'ttl' => 30
    ]
];
```

---

## Sistem Keamanan

MethodBus menggunakan beberapa lapisan keamanan:

| Security Layer    | Fungsi                      |
| ----------------- | --------------------------- |
| API Key           | Autentikasi client          |
| Signature         | Validasi integritas payload |
| Timestamp         | Mencegah request kadaluarsa |
| Nonce             | Mencegah replay request     |
| Replay Protection | Menolak request duplikat    |

---

## Header Wajib

Semua request HTTP maupun CLI wajib mengirim header berikut:

| Header       | Wajib    | Keterangan                         |
| ------------ | -------- | ---------------------------------- |
| Content-Type | Ya       | `application/json`                 |
| X-Method     | Ya       | Format: `namespace:action:version` |
| X-ApiKey     | Ya       | API authentication key             |
| X-Timestamp  | Ya       | Unix timestamp                     |
| X-Nonce      | Ya       | Unique random identifier           |
| X-Signature  | Ya       | HMAC SHA256 signature              |
| X-Request-ID | Optional | Request tracing                    |

---

## Konsep Signature

MethodBus menggunakan HMAC SHA256 untuk memastikan payload tidak dimodifikasi saat transit.

Signature dibentuk dari:

```text
json(sorted_payload) + timestamp + nonce
```

Setelah key diurutkan kemudian di-hash menggunakan shared secret yang sama dengan server.

---

## Shared Secret

Secret client dan server wajib sama.

Contoh konfigurasi:

```php
'signature' => [
    'enabled' => true,
    'secret' => 'super-secret-signature-key'
]
```

---

## Timestamp Validation

Timestamp digunakan untuk mencegah request lama digunakan ulang.

Contoh:

```php
$timestamp = (string) time();
```

TTL request diatur melalui:

```php
'replay' => [
    'enabled' => true,
    'ttl' => 30
]
```

Artinya request hanya valid selama:

```text
30 detik
```

---

## Nonce Validation

Nonce adalah identifier unik per request.

Nonce digunakan untuk mencegah replay attack.

Contoh:

```php
$nonce = bin2hex(
    random_bytes(16)
);
```

Setiap request wajib menggunakan nonce baru.

---

## Generate Signature (PHP)

```php
ksort($payload);

$message =
    json_encode($payload)
    . $timestamp
    . $nonce;

$signature = hash_hmac(
    'sha256',
    $message,
    'super-secret-signature-key'
);
```

---

## HTTP Basic

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MethodBus\Core\Http;
use MethodBus\Core\MethodBus;

$bus = new MethodBus();

$http = new Http($bus);

$http->handle();
```

---

## HTTP Custom

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MethodBus\Core\Http;
use MethodBus\Core\MethodBus;

$bus = new MethodBus();

$http = new Http($bus);

$http
    ->endpoint('/api')
    ->methodHeader('X-Method')
    ->handle();
```

---

## CLI Basic

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;

$bus = new MethodBus();
$payload = ['a' => 10, 'b' => 20];
$timestamp = (string) time();
$nonce = bin2hex( random_bytes(16));

function sign(array $payload, string $timestamp, string $nonce): string {
    ksort($payload);
    $message = json_encode($payload) . $timestamp . $nonce;

    return hash_hmac(
        'sha256',
        $message,
        'super-secret-signature-key'
    );
}

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

$result = $bus->call( 'math:add:v1', $payload, $context);
print_r($result);
```

---

## Dokumentasi API

MethodBus mendukung dokumentasi otomatis berbasis metadata plugin.

| Method             | Keterangan                 |
| ------------------ | -------------------------- |
| system:docs:latest | Dokumentasi versi terbaru  |
| system:docs:v1     | Dokumentasi plugin versi 1 |
| system:docs:v2     | Dokumentasi plugin versi 2 |

---

## Contoh Plugin

```php
<?php

namespace MethodBus\Plugins\Math;

use MethodBus\Contracts\PluginInterface;
use MethodBus\Contracts\PluginDocumentedInterface;
use MethodBus\Plugins\Math\Services\MathService;

final class AddPluginV1 implements PluginInterface, PluginDocumentedInterface
{
    public function __construct(
        private MathService $math
    ) {}

    public static function namespace(): string
    {
        return 'math';
    }

    public static function action(): string
    {
        return 'add';
    }

    public static function version(): string
    {
        return 'v1';
    }

    public static function method(): string
    {
        return sprintf(
            '%s:%s:%s',
            static::namespace(),
            static::action(),
            static::version()
        );
    }

    public function handle(array $payload): array
    {
        return [
            'result' => $this->math->add(
                $payload['a'],
                $payload['b']
            )
        ];
    }

    public static function docs(): array
    {
        return [
            'x-method' => static::method(),
            'namespace' => static::namespace(),
            'action' => static::action(),
            'version' => static::version(),
            'description' => 'Menjumlahkan dua angka'
        ];
    }
}
```

---

## Postman Pre-request Script

```javascript
const secret = "super-secret-signature-key";

const timestamp =
    Math.floor(Date.now() / 1000)
    .toString();

const nonce =
    crypto.randomUUID();

let body =
    JSON.parse(pm.request.body.raw);

let sorted = {};

Object.keys(body)
    .sort()
    .forEach(key => {
        sorted[key] = body[key];
    });

const message =
    JSON.stringify(sorted)
    + timestamp
    + nonce;

const signature =
    CryptoJS.HmacSHA256(
        message,
        secret
    ).toString();

pm.environment.set(
    "timestamp",
    timestamp
);

pm.environment.set(
    "nonce",
    nonce
);

pm.environment.set(
    "signature",
    signature
);
```

---

## Header Postman

```http
Content-Type : application/json
X-Method     : math:add:v1
X-ApiKey     : secret-key-123
X-Timestamp  : {{timestamp}}
X-Nonce      : {{nonce}}
X-Signature  : {{signature}}
```

---

## Body Request

```json
{
  "a": 10,
  "b": 20
}
```

---

## Cara Kerja Request Lifecycle

1. Request masuk ke HTTP Gateway
2. Kernel membuat Request object
3. Middleware Pipeline dijalankan:

   * API Key Validation
   * Timestamp Validation
   * Nonce Validation
   * Signature Validation
4. Plugin di-resolve melalui Plugin Manager
5. Container membuat instance plugin
6. Plugin dieksekusi oleh Plugin Runner
7. Response dikembalikan ke client

---

## Catatan Arsitektur

MethodBus menggunakan pendekatan:

* Metadata Registry
* Static Plugin Metadata
* Runtime Plugin Resolution
* Constructor Injection
* Lazy Loading
* Middleware Pipeline

Metadata plugin bersifat static agar:

* bootstrap lebih ringan
* memory usage lebih kecil
* dokumentasi lebih cepat di-generate

Sedangkan instance plugin baru dibuat saat runtime melalui container.