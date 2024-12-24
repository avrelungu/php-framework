<?php

namespace AurelLungu\Framework\Tests;

class DependentClass
{
    public function __construct(private DependencyClass $dependency)
    {
        
    }

    public function getDependency(): DependencyClass
    {
        return $this->dependency;
    }
}