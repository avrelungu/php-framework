<?php

use AurelLungu\Framework\Http\Kernel;
use AurelLungu\Framework\Http\Request;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$container = require BASE_PATH . '/config/services.php';

// receive request
$request = Request::createFromGlobals();
// perform some logic
$kernel = $container->get(Kernel::class);

$response = $kernel->handle($request);
// send response

$response->send();