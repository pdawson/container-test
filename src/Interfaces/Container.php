<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Interfaces;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * A simple container interface
 *
 * @package PaulDawson\ContainerTest
 */
interface Container extends PsrContainerInterface
{
    /**
     * Creates a new instance of the abstract using the container with the optional parameters.
     *
     * @param string $abstract
     * @param array $parameters
     *
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * Registers a singleton abstract against the container.
     *
     * @param string $abstract
     * @param Closure $concrete
     */
    public function singleton(string $abstract, Closure $concrete): void;

    /**
     * Registers a new abstract definition against the container.
     *
     * @param string $abstract
     * @param Closure $concrete
     */
    public function register(string $abstract, Closure $concrete): void;
}