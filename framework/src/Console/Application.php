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

        $args = array_slice($argv, 2);

        $options = $this->parseOptions($args);

        $status = $commandClass->execute($options);

        return $status;
    }

    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $option = explode('=', substr($arg, 2));
                $options[$option[0]] = $option[1] ?? true;

            }
        }

        return $options;
    }
}