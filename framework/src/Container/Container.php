<?php

namespace AurelLungu\Framework\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function add(string $id, string|object $concrete = null)
    {
        if (null === $concrete) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be added");
            }

            $concrete = $id;
        }

        $this->services[$id] = $concrete;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be resolved");
            }

            $this->add($id);
        }

        $object = $this->resolve($this->services[$id]);

        return $object;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services) && class_exists($this->services[$id]);
    }

     private function resolve($class): object
    {
        // 1. Instantiate a Reflection class (dump and check)
        $reflectionClass = new \ReflectionClass($class);

        // 2. Use Reflection to try to obtain a class constructor
        $constructor = $reflectionClass->getConstructor();

        // 3. If there is no constructor, simply instantiate
        if (null === $constructor) {
            return $reflectionClass->newInstance();
        }

        // 4. Get the constructor parameters
        $constructorParams = $constructor->getParameters();

        // 5. Obtain dependencies
        $classDependencies = $this->resolveClassDependencies($constructorParams);

        // 6. Instantiate with dependencies
        $service = $reflectionClass->newInstanceArgs($classDependencies);

        // 7. Return the object
        return $service;
    }

    private function resolveClassDependencies(array $reflectionParameters): array
    {
        // 1. Initialize empty dependencies array (required by newInstanceArgs)
        $classDependencies = [];

        // 2. Try to locate and instantiate each parameter
        /** @var \ReflectionParameter $parameter */
        foreach ($reflectionParameters as $parameter) {

            // Get the parameter's ReflectionNamedType as $serviceType
            $serviceType = $parameter->getType();

            // Try to instantiate using $serviceType's name
            $service = $this->get($serviceType->getName());

            // Add the service to the classDependencies array
            $classDependencies[] = $service;
        }

        // 3. Return the classDependencies array
        return $classDependencies;
    }
}
