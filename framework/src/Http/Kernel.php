<?php

namespace AurelLungu\Framework\Http;

use AurelLungu\Framework\Routing\RouterInterface;
use Exception;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv = 'dev';

    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container,
    ) {
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request): Response
    {
        try {
            [$handler, $vars] = $this->router->dispatch($request, $this->container);

            $response = call_user_func_array($handler, $vars);
        } catch (\Throwable $exception) {
            $response = $this->createExceptionResponse($exception);
        }
        return $response;
    }

    private function createExceptionResponse(\Exception $exception): Response
    {
        if (in_array($this->appEnv, ['dev', 'test'])) {
            throw $exception;
        }

        if ($exception instanceof HttpException) {
            return new Response($exception->getMessage(), $exception->getStatusCode());
        }

        return new Response('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}