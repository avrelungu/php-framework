<?php

namespace AurelLungu\Framework\Console;

use AurelLungu\Framework\Console\Command\CommandInterface;
use Psr\Container\ContainerInterface;

final class Kernel
{
    public function __construct(
        private ContainerInterface $container,
        private Application $application
    ) {
        
    }

    public function handle(): int
    {
        // Register commands
        $this->registerCommands();

        // Run the console application, returning a status code
        $status = $this->application->run();

        // Return status code
        return $status;
    }

    private function registerCommands(): void
    {
        // === Register all built in commands
        $commandFiles = new \DirectoryIterator(__DIR__ . '/Command');

        $namespace = $this->container->get('base-commands-namespace');

        foreach ($commandFiles as $commandFile) {
            if (!$commandFile->isFile()) {
                continue;
            }

            $command = $namespace.pathinfo($commandFile, PATHINFO_FILENAME);

            if (is_subclass_of($command, CommandInterface::class)) {
                $commandName = (new \ReflectionClass($command))->getProperty('name')->getDefaultValue();

                $this->container->add($commandName, $command);
            }
        }
        // === Register all user built command
    }
}