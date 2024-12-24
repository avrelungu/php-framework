<?php

use AurelLungu\Framework\Http\Kernel;
use AurelLungu\Framework\Http\Request;
use AurelLungu\Framework\Routing\Router;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$container = require BASE_PATH . '/config/services.php';

// receive request
$request = Request::createFromGlobals();

// perform some logic
$kernel = new Kernel(new Router());

$response = $kernel->handle($request);
// send response

$response->send();