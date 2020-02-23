<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;

abstract class Middleware {

    /**
     * I don't like passing a reference to a full container - this is an anit-pattern.
     * I have made it optional...
     * 
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}