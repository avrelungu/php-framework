<?php

namespace AurelLungu\Framework\Http;

use AurelLungu\Framework\Routing\RouterInterface;

class Kernel
{
    public function __construct(
        private RouterInterface $router
    ) {
        
    }

    public function handle(Request $request): Response
    {
        try {
            [$handler, $vars] = $this->router->dispatch($request);

            $response = call_user_func_array($handler, $vars);
        } catch (HttpException $exception) {
            $response = new Response($exception->getMessage(), $exception->getStatusCode());
        } catch (\Throwable $exception) {
            $response = new Response($exception->getMessage(), 500);
        }

        return $response;
    }
}