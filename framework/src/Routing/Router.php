<?php

namespace AurelLungu\Framework\Routing;

use AurelLungu\Framework\Http\HttpException;
use AurelLungu\Framework\Http\HttpMethodNotAllowedException;
use AurelLungu\Framework\Http\Request;
use AurelLungu\Framework\Http\Response;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;

use function FastRoute\simpleDispatcher;

class Router implements RouterInterface
{
    private array $routes = [];

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function dispatch(Request $request, ContainerInterface $container): array
    {
        $routeInfo = $this->extractRouteInfo($request);
        
        [$handler, $vars] = $routeInfo;

        if (is_array($handler)) {
            [$controllerId, $controllerMethod] = $handler;

            $controller = $container->get($controllerId);

            $handler = [$controller, $controllerMethod];
        }

        return [$handler, $vars];
    }

    private function extractRouteInfo(Request $request): array
    {
        // Create a Dispatcher
        $dispatcher = simpleDispatcher(function (RouteCollector $routeCollector) {
            foreach ($this->routes as $route) {
                $routeCollector->addRoute(...$route);
            }
        });

        // Dispatch a URI, to obtain the route info
        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPathInfo()
        );

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                return [$routeInfo[1], $routeInfo[2]]; // routeHandler, vars
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode('. ', $routeInfo[1]);
                $exception = new HttpMethodNotAllowedException("The allowed methods are $allowedMethods");
                $exception->setStatusCode(Response::METHOD_NOT_ALLOWED);

                throw $exception;
            default:
                $exception = new HttpException('Not Found');
                $exception->setStatusCode(Response::ROUTE_NOT_FOUND);

                throw $exception;
        }

        return $routeInfo;
    }
}
