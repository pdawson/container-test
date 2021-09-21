<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;
use PaulDawson\ContainerTest\Interfaces\Container as ContainerInterface;

/**
 * A basic, crude container implementation to teach the fundamentals of how
 * a service container works.
 *
 * @package PaulDawson\ContainerTest
 */
class Container implements ContainerInterface, ArrayAccess
{
    /**
     * The containers singleton instance
     *
     * @var self
     */
    protected static self $instance;

    /**
     * Stores the containers abstract definitions
     *
     * @var array
     */
    protected array $definitions = [];

    /**
     * Stores the containers singleton instances
     *
     * @var array
     */
    protected array $instances = [];

    /**
     * Returns the containers static instance
     *
     * @return self
     */
    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Creates a new abstract from the container with the given parameters.
     *
     * @param string $abstract
     * @param array $parameters
     *
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        $definition = $this->definitions[$abstract] ?? $this->resolve($abstract, $parameters);

        return $definition($this, $parameters);
    }

    /**
     * Registers a new shared abstract with the container
     *
     * @param string $abstract
     * @param Closure $concrete
     */
    public function singleton(string $abstract, Closure $concrete): void
    {
        $this->register($abstract, function () use ($abstract, $concrete) {
            if (array_key_exists($abstract, $this->instances)) {
                return $this->instances[$abstract];
            }

            $this->instances[$abstract] = $concrete($this);

            return $this->instances[$abstract];
        });
    }

    /**
     * Registers the given abstract with the container
     *
     * @param string $abstract
     * @param Closure $concrete
     */
    public function register(string $abstract, Closure $concrete): void
    {
        $this->definitions[$abstract] = $concrete;
    }

    /**
     * Attempts to resolve an abstract via the container using reflection to auto-wire
     * any dependencies the abstract may have.
     *
     * @param string $abstract
     * @param array $parameters
     *
     * @return Closure
     */
    protected function resolve(string $abstract, array $parameters = []): Closure
    {
        return function () use ($abstract, $parameters) {
            $class = new ReflectionClass($abstract);

            $arguments = $class->getConstructor() !== null
                ? $class->getConstructor()->getParameters()
                : [];

            if (count($parameters) === count($arguments)) {
                return new $abstract(...$parameters);
            }

            $arguments = array_slice(array_merge($parameters, $arguments), 0, count($arguments));

            $dependencies = array_map(
                function (mixed $parameter) {
                    if ($parameter instanceof ReflectionParameter) {
                        return $this->make($parameter->getType()->getName());
                    }

                    return $parameter;
                },
                $arguments
            );

            return new $abstract(...$dependencies);
        };
    }

    /**
     * Retrieves a registered item from the container using make.
     * Where the abstract is a singleton, the instance is returned.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->make($id);
    }

    /**
     * Determines if the container has the given abstract registered
     *
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * Determines if the given item exists on the container
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Retrieves a registered item from the container
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Registers a new item with the container for the given offset (abstract)
     * and value (concrete).
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->register($offset, $value);
    }

    /**
     * Removes a registered item from the container including any instances
     *
     * @param array-key|mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->instances[$offset], $this->definitions[$offset]);
    }
}