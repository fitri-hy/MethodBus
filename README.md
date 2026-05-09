# MethodBus: PHP Service Bus & Single-Endpoint Gateway

**MethodBus** adalah library PHP yang mengimplementasikan pola *Service Bus* dengan arsitektur *Single-Endpoint*. Library ini berfungsi sebagai pusat kontrol (Gateway) yang menerima semua request melalui satu pintu, melakukan validasi keamanan ketat menggunakan HMAC-SHA256, dan mendistribusikan eksekusi ke unit fungsional (Plugins).

## ⚙️ Core Architecture

* **Single Entry Point**: Seluruh trafik HTTP diarahkan ke satu endpoint tunggal.
* **Header-Based Routing**: Resolusi fungsi menggunakan header `X-Method` (`namespace:action:version`).
* **Strict Security**: Verifikasi integritas payload wajib menggunakan API Key dan HMAC Signature.
* **Versioned Documentation**: Mendukung `system:docs` dengan kontrol versi (`latest`, `v1`, dst).

---

## 🛠 API Reference (Header Specifications)

Semua request (HTTP maupun CLI) wajib memenuhi spesifikasi header berikut:

| Header | Status | Description |
| --- | --- | --- |
| `Content-Type` | **Required** | Wajib `application/json`. |
| `X-Method` | **Required** | Identifier rute. Format: `{namespace}:{action}:{version}`. |
| `X-ApiKey` | **Required** | Token autentikasi statis sesuai konfigurasi server. |
| `X-Signature` | **Required** | HMAC-SHA256 dari payload yang sudah di-`ksort`. |
| `X-Request-ID` | Optional | ID unik untuk tracking log/request tracing. |

---

## 🚀 Implementasi HTTP Mode

### 1. HTTP Basic Setup

Gunakan ini untuk setup cepat gateway API pada file entry point Anda (misal: `api.php`).

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MethodBus\Core\MethodBus;
use MethodBus\Core\Http;

$bus = new MethodBus();
$http = new Http($bus);

// Menjalankan HTTP lifecycle secara standar
$http->handle();

```

### 2. HTTP Custom Setup (Advanced)

Gunakan ini jika Anda perlu menentukan prefix rute atau memastikan header yang digunakan secara eksplisit.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MethodBus\Core\MethodBus;
use MethodBus\Core\Http;

$bus = new MethodBus();
$http = new Http($bus);

$http
    ->endpoint('/api')          // Prefix rute (optional)
    ->methodHeader('X-Method')  // Routing header plugin
    ->handle();                 // Eksekusi request lifecycle

```

---

## 💻 Implementasi CLI Mode

Mode ini digunakan untuk eksekusi langsung dari command line, background job, atau komunikasi antar-service internal.

### CLI dengan Context Reusable

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use MethodBus\Core\MethodBus;

$bus = new MethodBus();

// Helper untuk generate signature
function sign(array $payload): string {
    ksort($payload); 
    return hash_hmac('sha256', json_encode($payload), 'super-secret-signature-key');
}

// Helper context builder
function makeContext(string $requestId, array $payload): array {
    return [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-ApiKey'     => 'secret-key-123',
            'X-Signature'  => sign($payload),
            'X-Request-ID' => $requestId
        ]
    ];
}

$payload = ['a' => 15, 'b' => 25];

// Eksekusi plugin via Bus call
$result = $bus->call(
    'math:add:v1',
    $payload,
    makeContext('cli-custom-002', $payload)
);

print_r($result);

```

---

## 📖 Versioned Documentation System

MethodBus mendukung pengambilan dokumentasi otomatis berdasarkan versi plugin yang terdaftar.

| X-Method | Deskripsi |
| --- | --- |
| `system:docs:latest` | Menampilkan dokumentasi versi terbaru dari seluruh plugin. |
| `system:docs:v1` | Menampilkan dokumentasi spesifik untuk plugin versi 1. |
| `system:docs:v2` | Menampilkan dokumentasi spesifik untuk plugin versi 2. |

---

## 🔒 Security Protocol (HMAC SHA256)

Untuk menjaga integritas data, client wajib melakukan normalisasi payload sebelum hashing.

**Langkah-langkah:**

1. **Sort**: Urutkan key payload secara alfabetis (`ksort`).
2. **Serialize**: Konversi ke JSON string padat.
3. **Sign**: Generate HMAC menggunakan SHA256 dengan *shared secret key*.

### Postman Pre-request Script:

```javascript
const secret = "super-secret-signature-key";
let body = pm.request.body.raw;
let json = JSON.parse(body);

// Normalisasi JSON (Sort by Key)
let sorted = {};
Object.keys(json).sort().forEach(k => sorted[k] = json[k]);

const signature = CryptoJS.HmacSHA256(
    JSON.stringify(sorted),
    secret
).toString();

pm.environment.set("signature", signature);

```