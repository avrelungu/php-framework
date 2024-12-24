<?php

namespace AurelLungu\Framework\Controller;

use AurelLungu\Framework\Http\Response;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    protected ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function render(string $template, array $parameters = [], Response $response = null): Response
    {
        $content = $this->container->get('twig')->render($template, $parameters);

        $response ??= new Response(); // Create a new Response object if $response is null

        $response->setContent($content);

        return $response;
    }
}