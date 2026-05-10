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