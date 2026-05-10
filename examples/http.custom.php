<?php

require __DIR__ . '/../vendor/autoload.php';

use MethodBus\Core\MethodBus;
use MethodBus\Core\Http;

/**
 * initialize MethodBus core
 */
$bus = new MethodBus();

/**
 * initialize HTTP gateway
 */
$http = new Http($bus);

/**
 * custom HTTP gateway configuration
 */
$http
    ->endpoint('/api')
    ->methodHeader('X-Method')
    ->handle();