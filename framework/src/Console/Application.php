<?php

namespace AurelLungu\Framework\Console;

use Psr\Container\ContainerInterface;

class Application
{
    public function __construct(private ContainerInterface $container)
    {
        
    }

    public function run()
    {
        $argv = $_SERVER['argv'];
        $commandName = $argv[1] ?? null;

        if (!$commandName) {
            throw new ConsoleException('A command name must be provided.');
        }

        $commandClass = $this->container->get($commandName) ?? null;

        if (!$commandClass)  {
            throw new ConsoleException('The provided command name is not valid.');
        }

        $status = $commandClass->execute();

        return $status;
    }
}