<?php

use AurelLungu\Framework\Console\Application;
use AurelLungu\Framework\Console\Command\MigrateDatabase;
use AurelLungu\Framework\Console\Kernel as ConsoleKernel;
use AurelLungu\Framework\Controller\AbstractController;
use AurelLungu\Framework\Dbal\ConnectionFactory;
use AurelLungu\Framework\Http\Kernel;
use AurelLungu\Framework\Routing\Router;
use AurelLungu\Framework\Routing\RouterInterface;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '/.env');

$container = new Container();

$container->delegate(new ReflectionContainer(true));

// Parameters

$appEnv = $_SERVER['APP_ENV'];
$container->add('APP_ENV', new StringArgument($appEnv));

$templatesPath = BASE_PATH . '/templates';

$databaseUrl = 'pdo-sqlite:///' . BASE_PATH . '/var/db.sqlite';

$container->add(
    'base-commands-namespace',
    new \League\Container\Argument\Literal\StringArgument('AurelLungu\\Framework\\Console\\Command\\')
);

$routes = include BASE_PATH . '/routes/web.php';

// Services
$container->add(RouterInterface::class, Router::class);

$container->extend(RouterInterface::class)
    ->addMethodCall('setRoutes', [new ArrayArgument($routes)]);

$container->add(Kernel::class)
    ->addArgument(RouterInterface::class)
    ->addArgument($container);

$container->addShared('file-system-loader', FilesystemLoader::class)
    ->addArgument(new StringArgument($templatesPath));

$container->addShared('twig', Environment::class)
    ->addArgument('file-system-loader');

$container->add(AbstractController::class);

$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

$container->add(ConsoleKernel::class)
    ->addArgument($container)
    ->addArgument(Application::class);

$container->add(Application::class)
    ->addArgument($container);

// Database
$container->add(ConnectionFactory::class)
    ->addArgument(new StringArgument($databaseUrl));
    
$container->addShared(Connection::class, function () use ($container): Connection {
    $connectionFactory = $container->get(ConnectionFactory::class);

    return $connectionFactory->create();
});

$container->add(
    'database:migrations:migrate',
    MigrateDatabase::class
)->addArgument(Connection::class)
->addArgument(BASE_PATH . '/migrations/');

return $container;