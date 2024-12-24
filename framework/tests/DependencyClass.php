<?php

namespace AurelLungu\Framework\Tests;

class DependencyClass
{
    public function __construct(private SubDependency $subDependency)
    {
        
    }

    public function getSubDependency()
    {
        return $this->subDependency;
    }
}