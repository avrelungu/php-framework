<?php

use App\Controller\HomeController;
use AurelLungu\Framework\Http\Kernel;
use AurelLungu\Framework\Routing\Router;
use AurelLungu\Framework\Routing\RouterInterface;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '/.env');

$container = new Container();

$container->delegate(new ReflectionContainer(true));

// Parameters

$appEnv = $_SERVER['APP_ENV'];
$container->add('APP_ENV', new StringArgument($appEnv));

$routes = include BASE_PATH . '/routes/web.php';

// Services
$container->add(RouterInterface::class, Router::class);

$container->extend(RouterInterface::class)
    ->addMethodCall('setRoutes', [new ArrayArgument($routes)]);

$container->add(Kernel::class)
    ->addArgument(RouterInterface::class)
    ->addArgument($container);

return $container;