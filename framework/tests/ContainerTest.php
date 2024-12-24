<?php

namespace AurelLungu\Framework\Tests;

use AurelLungu\Framework\Container\Container;
use AurelLungu\Framework\Container\ContainerException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function test_a_service_can_be_retrieved_from_the_container()
    {
        // Setup
        $container = new Container();

        // Do something
        // id string, concrete class name string | object
        $container->add('dependant-class', DependentClass::class);

        // Make assertions
        $this->assertInstanceOf(DependentClass::class, $container->get('dependant-class'));
    }

    /** @test */
    public function test_a_ContainerException_is_thrown_if_a_service_cannot_be_found()
    {
        // Setup
        $container = new Container();

        // Expect exception
        $this->expectException(ContainerException::class);

        // Do something
        $container->add('foobar');
    }

    /** @test */
    public function test_can_check_if_the_container_has_a_service(): void
    {
        // Setup 
        $container = new Container();

        // Do something
        $container->add('dependant-class', DependentClass::class);

        $this->assertTrue($container->has('dependant-class'));
        $this->assertFalse($container->has('non-existent-class'));
    }

    /** @test */
    public function test_services_can_be_recursively_autowired()
    {
        $container = new Container();

        $dependantService = $container->get(DependentClass::class);

        $dependancyService = $dependantService->getDependency();

        $this->assertInstanceOf(DependencyClass::class, $dependancyService);
        $this->assertInstanceOf(SubDependency::class, $dependancyService->getSubDependency());
    }
}