<?php

return [
    'middleware' => [
        \MethodBus\Middleware\AuthMiddleware::class,
        \MethodBus\Middleware\ValidationMiddleware::class
    ]
];